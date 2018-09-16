<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.4.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Enum;
use Bit\Vars\Enum;

/**
 * Enum Lang
 * @package Bit\Enum
 */
class Lang extends Enum{
    /*
     * af	Afrikaans
     * sq	Albanian
     * ar	Arabic (Standard)
     * ar-dz	Arabic (Algeria)
     * ar-bh	Arabic (Bahrain)
     * ar-eg	Arabic (Egypt)
     * ar-iq	Arabic (Iraq)
     * ar-jo	Arabic (Jordan)
     * ar-kw	Arabic (Kuwait)
     * ar-lb	Arabic (Lebanon)
     * ar-ly	Arabic (Libya)
     * ar-ma	Arabic (Morocco)
     * ar-om	Arabic (Oman)
     * ar-qa	Arabic (Qatar)
     * ar-sa	Arabic (Saudi Arabia)
     * ar-sy	Arabic (Syria)
     * ar-tn	Arabic (Tunisia)
     * ar-ae	Arabic (U.A.E.)
     * ar-ye	Arabic (Yemen)
     * ar	Aragonese
     * hy	Armenian
     * as	Assamese
     * ast	Asturian
     * az	Azerbaijani
     * eu	Basque
     * bg	Bulgarian
     * be	Belarusian
     * bn	Bengali
     * bs	Bosnian
     * br	Breton
     * bg	Bulgarian
     * my	Burmese
     * ca	Catalan
     * ch	Chamorro
     * ce	Chechen
     * zh	Chinese
     * zh-hk	Chinese (Hong Kong)
     * zh-cn	Chinese (PRC)
     * zh-sg	Chinese (Singapore)
     * zh-tw	Chinese (Taiwan)
     * cv	Chuvash
     * co	Corsican
     * hr	Croatian
     * cs	Czech
     * da	Danish
     * nl	Dutch (Standard)
     * nl-be	Dutch (Belgian)
     */
    const EN	= 'en'; //English
    const EN_AU	= 'en'; //English (Australia)	
    const EN_BZ	= 'en'; //English (Belize)
    const EN_CA	= 'en'; //English (Canada)
    const EN_IE	= 'en'; //English (Ireland)
    const EN_JM	= 'en'; //English (Jamaica)
    const EN_NZ	= 'en'; //English (New Zealand)
    const EN_PH	= 'en'; //English (Philippines)
    const EN_ZA	= 'en'; //English (South Africa)
    const EN_TT	= 'en'; //English (Trinidad & Tobago)
    const EN_GB	= 'en'; //English (United Kingdom)
    const EN_US	= 'en'; //English (United States)
    const EN_ZW	= 'en'; //English (Zimbabwe)
    /*
     * eo	Esperanto
     * et	Estonian
     * fo	Faeroese
     * fa	Farsi
     * fj	Fijian
     * fi	Finnish
     */
    const FR	= 'fr'; //French (Standard)
    const FR_BE	= 'fr'; //French (Belgium)
    const FR_CA	= 'fr'; //French (Canada)
    const FR_FR	= 'fr'; //French (France)
    const FR_LU	= 'fr'; //French (Luxembourg)
    const FR_MC	= 'fr'; //French (Monaco)
    const FR_CH	= 'fr'; //French (Switzerland)
    
    /*
     * fy	Frisian
     * fur	Friulian
     * gd	Gaelic (Scots)
     * gd-ie	Gaelic (Irish)
     * gl	Galacian
     * ka	Georgian     * 
     */
    const DE	= 'de'; //German (Standard)
    const DE_AT	= 'de'; //German (Austria)
    const DE_DE	= 'de'; //German (Germany)
    const DE_LI	= 'de'; //German (Liechtenstein)	
    const DE_LU	= 'de'; //German (Luxembourg)
    const DE_CH	= 'de'; //German (Switzerland)
    /*
     * el	Greek
     * gu	Gujurati
     * ht	Haitian
     * he	Hebrew
     * hi	Hindi
     * hu	Hungarian
     * is	Icelandic
     * id	Indonesian
     * iu	Inuktitut
     * ga	Irish
     */
    const IT	= 'it'; //Italian (Standard)
    const IT_CH	= 'it'; //Italian (Switzerland)
    /*
     * ja	Japanese
     * kn	Kannada
     * ks	Kashmiri
     * kk	Kazakh
     * km	Khmer
     * ky	Kirghiz
     * tlh	Klingon
     * ko	Korean
     * ko-kp	Korean (North Korea)
     * ko-kr	Korean (South Korea)
     * la	Latin
     * lv	Latvian
     * lt	Lithuanian
     * lb	Luxembourgish
     * mk	FYRO Macedonian
     * ms	Malay
     * ml	Malayalam
     * mt	Maltese
     * mi	Maori
     * mr	Marathi
     * mo	Moldavian
     * nv	Navajo
     * ng	Ndonga
     * ne	Nepali
     * no	Norwegian
     * nb	Norwegian (Bokmal)
     * nn	Norwegian (Nynorsk)
     * oc	Occitan
     * or	Oriya
     * om	Oromo
     * fa	Persian
     * fa-ir	Persian/Iran
     * pl	Polish
     * pt	Portuguese
     * pt-br	Portuguese (Brazil)
     * pa	Punjabi
     * pa-in	Punjabi (India)
     * pa-pk	Punjabi (Pakistan)
     * qu	Quechua
     * rm	Rhaeto-Romanic
     * ro	Romanian
     * ro-mo	Romanian (Moldavia)
     * ru	Russian
     * ru-mo	Russian (Moldavia)
     * sz	Sami (Lappish)
     * sg	Sango
     * sa	Sanskrit
     * sc	Sardinian
     * gd	Scots Gaelic
     * sd	Sindhi
     * si	Singhalese
     * sr	Serbian
     * sk	Slovak
     * sl	Slovenian
     * so	Somani
     * sb	Sorbian
     * es	Spanish
     * es-ar	Spanish (Argentina)
     * es-bo	Spanish (Bolivia)
     * es-cl	Spanish (Chile)
     * es-co	Spanish (Colombia)
     * es-cr	Spanish (Costa Rica)
     * es-do	Spanish (Dominican Republic)
     * es-ec	Spanish (Ecuador)
     * es-sv	Spanish (El Salvador)
     * es-gt	Spanish (Guatemala)
     * es-hn	Spanish (Honduras)
     * es-mx	Spanish (Mexico)	
     * es-ni	Spanish (Nicaragua)
     * es-pa	Spanish (Panama)	
     * es-py	Spanish (Paraguay)
     * es-pe	Spanish (Peru)
     * es-pr	Spanish (Puerto Rico)
     * es-es	Spanish (Spain)
     * es-uy	Spanish (Uruguay)
     * es-ve	Spanish (Venezuela)
     * sx	Sutu
     * sw	Swahili
     * sv	Swedish
     * sv-fi	Swedish (Finland)
     * sv-sv	Swedish (Sweden)
     * ta	Tamil
     * tt	Tatar
     * te	Teluga
     * th	Thai
     * tig	Tigre
     * ts	Tsonga
     * tn	Tswana
     * tr	Turkish
     * tk	Turkmen
     * uk	Ukrainian
     * hsb	Upper Sorbian
     * ur	Urdu
     * ve	Venda
     * vi	Vietnamese
     * vo	Volapuk
     * wa	Walloon
     * cy	Welsh
     * xh	Xhosa
     * ji	Yiddish
     * zu	Zulu 
     */				
}