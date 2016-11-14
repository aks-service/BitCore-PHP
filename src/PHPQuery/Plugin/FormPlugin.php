<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 09.06.16
 * Time: 22:08
 */

namespace Bit\PHPQuery\Plugin;

use Bit\Core\Configure;
use Bit\Network\Request;
use Bit\PHPQuery\Plugin as BasePlugin;
use Bit\PHPQuery\QueryObject;

use Bit\Utility\Hash;
use Bit\View\Helper\SecureFieldTokenTrait;


class FormPlugin extends BasePlugin
{
    use SecureFieldTokenTrait;

    protected $_defaultConfig = [
        'selector' => 'form',
        'template' => [
            'hidden' => '<input type="hidden" $s>'
        ]
    ];


    /**
     * Generates a hidden field with a security hash based on the fields used in
     * the form.
     *
     * If $secureAttributes is set, these HTML attributes will be merged into
     * the hidden input tags generated for the Security Component. This is
     * especially useful to set HTML5 attributes like 'form'.
     *
     * @param array $fields If set specifies the list of fields to use when
     *    generating the hash, else $this->fields is being used.
     * @param array $secureAttributes will be passed as HTML attributes into the hidden
     *    input elements generated for the Security Component.
     * @return string A hidden input field with a security hash, or empty string when
     *   secured forms are not in use.
     */
    protected function secure(QueryObject $qrx, Request $request, array $secureAttributes = [])
    {
        $debugSecurity = Configure::read('debug');
        if (isset($secureAttributes['debugSecurity'])) {
            $debugSecurity = $debugSecurity && $secureAttributes['debugSecurity'];
            unset($secureAttributes['debugSecurity']);
        }

        $url = $request->here();

        $unlockFields = $fields = [];

        $fileUpload = false;

        $qrx->filter('input[name],button[name],select[name],textarea[name]')->each(function (QueryObject $node) use ($request, &$fields, &$unlockFields,&$fileUpload){
            $type = $node->attr('type');

            $field = $node->attr('name');
            $value = $node->attr('value');
            $lock = $node->attr('data-secure');

            if ($type === 'checkbox')
                $lock = 'skip';
            else if ($type === 'file')
                $fileUpload = true;

            if (is_string($field)) {
                $field = Hash::filter(explode('.', $field));
            }

            $field = implode('.', $field);
            $field = preg_replace('/(\.\d+)+$/', '', $field);

            if ($lock !== 'skip') {
                if (!in_array($field, $fields)) {
                    if ($value !== null) {
                        return $fields[$field] = $value;
                    }

                    if (isset($fields[$field]) && $value === null) {
                        unset($fields[$field]);
                    }
                    $fields[] = $field;
                }
            } else {
                $unlockFields[] = $field;
            }
        });
        if($fileUpload)
             $qrx->attr('enctype',"multipart/form-data");

        //var_dump([$url,$fields,$unlockFields]);

        $tokenData = $this->_buildFieldToken(
            $url,
            $fields,
            $unlockFields
        );
        /*var_dump($tokenData);
        die();
        /*var_dump($qrx);
        die();*/

        $qrx->append('<input type="hidden" name="_Token[fields]" value="' . $tokenData['fields'] . '">');
        $qrx->append('<input type="hidden" name="_Token[unlocked]" value="' . $tokenData['unlocked'] . '">');

        if ($debugSecurity) {
            $qrx->append('<input type="hidden" name="_Token[debug]" value="' . urlencode(json_encode([
                    $this->_lastAction,
                    $fields,
                    $this->_unlockedFields
                ])) . '">');
        }
    }

    public function invoke(QueryObject $query, $_args)
    {
        //

        if ($_args[0] instanceof Request) {
            $request = array_shift($_args);
            $secure = count($_args) ? array_shift($_args) : [];
            $query->filter($this->config('selector'))->each(function (QueryObject $node) use ($request, $secure) {
                $method = $node->attr('method');
                $node->attr('action',$node->attr('action')?: $request->here(false));

                if (!empty($request->params['_csrfToken'])) {
                    if ($method === 'post') {
                        $node->prepend('<input type="hidden" name="_csrfToken" value="' . $request->params['_csrfToken'] . '">');

                    }
                }

                if (!empty($request['_Token'])) {
                    $this->secure($node, $request, $secure);
                }
                /*var_dump($method);
                    die();
                $node->prepend('<input>');*/
            });

            /*var_dump([$this,$query,$request->params]);

            die();*/
        }
        /*var_dump($_args , $_args[0] instanceof Request);

        die('HMM');*/
        return $query;
    }
}