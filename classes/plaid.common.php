<?php
class PlaidIntCommon
{
	var $wp_all_categories =  array();
	
	var $options ;
	
	public function __construct(){
		
		$this->options = get_option('plaidintegra_options');		
	
	}
	
	
	function get_all_sytem_pages()
	{
	    if($this->wp_all_pages === false)
	    {
	        $this->wp_all_pages[0] = "Select Page";
	        foreach(get_pages() as $key=>$value)
	        {
	            $this->wp_all_pages[$value->ID] = $value->post_title;
	        }
	    }
	    
	    return $this->wp_all_pages;
	}
	
	function get_all_sytem_cagegories()
	{
		
		
		$args = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false 
		
		); 

		
		$categories = get_categories($args); 


	    
	        $this->wp_all_categories[0] = "Select Category";
	        foreach($categories as $category)
	        {
	            $this->wp_all_categories[$category->cat_ID] = $category->cat_name;
	        }
	    
	    
	    return $this->wp_all_categories;
	}
	
	// get value in admin option
    function get_value($option_id) 
	{
		
		$this->options = get_option('plaidintegra_options');


        if (isset($this->options[$option_id]) && $this->options[$option_id] != '' ) 
		{
			
			if(is_string($this->options[$option_id])){
				
				 return stripslashes($this->options[$option_id]);
			
			}else{
				
				 return $this->options[$option_id];
			}
           
			
        } elseif (isset($this->bup_default_options[$option_id]) && $this->bup_default_options[$option_id] != '' ) {
            return stripslashes($this->bup_default_options[$option_id]);
        } else {
            return null;
        }
    }
	
		// add setting field
    function create_plugin_setting($type, $id, $label, $pairs, $help, $inline_help = '', $extra=null) {
		
		global  $ultimatecaptcha;
		$qtip_style = '';

        $field_holder_id= $id.'_holder';
        echo  "<tr valign=\"top\" id=\"$field_holder_id\">
        <th scope=\"row\"><label for=\"$id\">$label</label></th>
        <td>";
        $input_html = '';

        $value = '';
        $value = $this->get_value($id);

        switch ($type) {

            case 'textarea':
                print "<textarea name=\"$id\" type=\"text\" id=\"$id\" class=\"large-text code text-area uultra-setting-options-texarea\" rows=\"3\">$value</textarea>";
                break;
			
			  case 'textarearich':
			  
              /*  print "<textarea name=\"$id\" type=\"text\" id=\"$id\" class=\"large-text code text-area uultra-setting-options-texarea\" rows=\"3\">$value</textarea>";*/
				
				$html = $ultimatecaptcha->get_me_wphtml_editor($id, $value);
				
				print $html;
				
                break;

            case 'input':
                print "<input name=\"$id\" type=\"text\" id=\"$id\" value=\"$value\" class=\"regular-text\" />";
                break;

            case 'select':
                print "<select name=\"$id\" id=\"$id\">";
                foreach($pairs as $k => $v) {

                    if (is_array($v)) {
                        $v = $v['name'];
                    }

                    echo '<option value="'.$k.'"';
                    if (isset($this->options[$id]) && $k == $this->options[$id]) {
                        echo ' selected="selected"';
                    }
                    echo '>';
                    echo $v;
                    echo '</option>';

                }
                print "</select>";
                break;

            case 'checkbox':
                $checked='';
                if('1' == $value)
                {
                    $checked='checked';
                }
                print "<input name=\"$id\" type=\"checkbox\" id=\"$id\" value=\"1\" ".$checked." />";
                break;
            case 'color':
                $default_color = $this->defaults[$id];
                print "<input name=\"$id\" type=\"text\" id=\"$id\" value=\"$value\" class=\"my-color-field\" data-default-color=\"$default_color\" />";
                break;
				
			case 'checkbox_list':
                $selected_roles = $value;
                $default_role = get_option("default_role");

                
                foreach ($pairs as $role_key => $role) {
                    if($default_role == $role_key){
                        echo $this->check_box(array('name' => $id.'[]', 'id' => $id, 'value' => $role_key,'checked'=>'checked','disabled'=>'disabled')).$role.'<br/>';            
                    }else{
                        $checked_value = '';
                        if(is_array($selected_roles) && in_array($role_key,$selected_roles)){
                            $checked_value = $role_key;
                        }
                        echo $this->check_box(array('name' => $id.'[]', 'id' => $id, 'value' => $role_key),$checked_value).$role.'<br/>';            
                    }
                    
                }
                break;

        }
		
		$qtip_classes = 'qtip-light ';

        if($inline_help!='')
        {
            echo '<a class="'.$qtip_classes.' uultra-tooltip" title="' . $inline_help . '" '.$qtip_style.'><i class="fa fa-info-circle reg_tooltip"></i></a>';
        }


        if ($help)
            echo "<p class=\"description\">$help</p>";

        if (is_array($extra)) {
            echo "<div class=\"helper-wrap\">";
            foreach ($extra as $a) {
                echo $a;
            }
            echo "</div>";
        }
        	
        echo "</td></tr>";

    }
	
	 public static function check_box($property=array(),$selected='0')
    {
        $chek_box='<input type="checkbox"';
        	
        $checked='';
        
        if(is_array($property))
        {
            foreach($property as $key=>$value)
            {
                if($key == 'value' && trim($value) == trim($selected))
                    $checked=' checked="checked"';
            
                $chek_box.=' '.$key.'="'.$value.'"';
            }    
        }
        
        $chek_box.=$checked.' />';
        return $chek_box;
    }
	
	function get_option($option) 
	{
		$settings = get_option('plaidintegra_options');
		if (isset($settings[$option])) 
		{
			return $settings[$option];
			
		}else{
			
		    return '';
		}
		    
	}
	
	public function fetch_result($results)
	{
		if ( empty( $results ) )
		{
		
		
		}else{
			
			
			foreach ( $results as $result )
			{
				return $result;			
			
			}
			
		}
		
	}

	
	/* Predefined arrays/listings */
	public function get_predifined($filter) 
	{
		$array = array();
	    
		switch($filter) {
			
			case 'countries':
				$array = array (
				  '0'  => '',
					'AF' => 'Afghanistan',
					'AX' => 'Aland Islands',
					'AL' => 'Albania',
					'DZ' => 'Algeria',
					'AS' => 'American Samoa',
					'AD' => 'Andorra',
					'AO' => 'Angola',
					'AI' => 'Anguilla',
					'AQ' => 'Antarctica',
					'AG' => 'Antigua and Barbuda',
					'AR' => 'Argentina',
					'AM' => 'Armenia',
					'AW' => 'Aruba',
					'AU' => 'Australia',
					'AT' => 'Austria',
					'AZ' => 'Azerbaijan',
					'BS' => 'Bahamas',
					'BH' => 'Bahrain',
					'BD' => 'Bangladesh',
					'BB' => 'Barbados',
					'BY' => 'Belarus',
					'BE' => 'Belgium',
					'BZ' => 'Belize',
					'BJ' => 'Benin',
					'BM' => 'Bermuda',
					'BT' => 'Bhutan',
					'BO' => 'Bolivia',
					'BA' => 'Bosnia and Herzegovina',
					'BW' => 'Botswana',
					'BV' => 'Bouvet Island',
					'BR' => 'Brazil',
					'IO' => 'British Indian Ocean Territory',
					'BN' => 'Brunei Darussalam',
					'BG' => 'Bulgaria',
					'BF' => 'Burkina Faso',
					'BI' => 'Burundi',
					'KH' => 'Cambodia',
					'CM' => 'Cameroon',
					'CA' => 'Canada',
					'CV' => 'Cape Verde',
					'KY' => 'Cayman Islands',
					'CF' => 'Central African Republic',
					'TD' => 'Chad',
					'CL' => 'Chile',
					'CN' => 'China',
					'CX' => 'Christmas Island',
					'CC' => 'Cocos (Keeling) Islands',
					'CO' => 'Colombia',
					'KM' => 'Comoros',
					'CG' => 'Congo',
					'CD' => 'Congo Democratic',
					'CK' => 'Cook Islands',
					'CR' => 'Costa Rica',
					'CI' => "Cote d'Ivoire",
					'HR' => 'Croatia',
					'CU' => 'Cuba',
					'CY' => 'Cyprus',
					'CZ' => 'Czech Republic',
					'DK' => 'Denmark',
					'DJ' => 'Djibouti',
					'DM' => 'Dominica',
					'DO' => 'Dominican Republic',
					'EC' => 'Ecuador',
					'EG' => 'Egypt',
					'SV' => 'El Salvador',
					'GQ' => 'Equatorial Guinea',
					'ER' => 'Eritrea',
					'EE' => 'Estonia',
					'ET' => 'Ethiopia',
					'FK' => 'Falkland Islands (Malvinas)',
					'FO' => 'Faroe Islands',
					'FJ' => 'Fiji',
					'FI' => 'Finland',
					'FR' => 'France',
					'GF' => 'French Guiana',
					'PF' => 'French Polynesia',
					'TF' => 'French Southern Territories',
					'GA' => 'Gabon',
					'GM' => 'Gambia',
					'GE' => 'Georgia',
					'DE' => 'Germany',
					'GH' => 'Ghana',
					'GI' => 'Gibraltar',
					'GR' => 'Greece',
					'GL' => 'Greenland',
					'GD' => 'Grenada',
					'GP' => 'Guadeloupe',
					'GU' => 'Guam',
					'GT' => 'Guatemala',
					'GG' => 'Guernsey',
					'GN' => 'Guinea',
					'GW' => 'Guinea-Bissau',
					'GY' => 'Guyana',
					'HT' => 'Haiti',
					'HM' => 'Heard Island and McDonald Islands',
					'VA' => 'Holy See (Vatican City State)',
					'HN' => 'Honduras',
					'HK' => 'Hong Kong',
					'HU' => 'Hungary',
					'IS' => 'Iceland',
					'IN' => 'India',
					'ID' => 'Indonesia',
					'IR' => 'Iran',
					'IQ' => 'Iraq',
					'IE' => 'Ireland',
					'IM' => 'Isle of Man',
					'IL' => 'Israel',
					'IT' => 'Italy',
					'JM' => 'Jamaica',
					'JP' => 'Japan',
					'JE' => 'Jersey',
					'JO' => 'Jordan',
					'KZ' => 'Kazakhstan',
					'KE' => 'Kenya',
					'KI' => 'Kiribati',
					'KP' => "Korea Democratic",
					'KR' => 'Korea Republic',
					'KW' => 'Kuwait',
					'KG' => 'Kyrgyzstan',
					'LA' => "Lao People's Democratic Republic",
					'LV' => 'Latvia',
					'LB' => 'Lebanon',
					'LS' => 'Lesotho',
					'LR' => 'Liberia',
					'LY' => 'Libya',
					'LI' => 'Liechtenstein',
					'LT' => 'Lithuania',
					'LU' => 'Luxembourg',
					'MO' => 'Macao',
					'MK' => 'Macedonia',
					'MG' => 'Madagascar',
					'MW' => 'Malawi',
					'MY' => 'Malaysia',
					'MV' => 'Maldives',
					'ML' => 'Mali',
					'MT' => 'Malta',
					'MH' => 'Marshall Islands',
					'MQ' => 'Martinique',
					'MR' => 'Mauritania',
					'MU' => 'Mauritius',
					'YT' => 'Mayotte',
					'MX' => 'Mexico',
					'FM' => 'Micronesia',
					'MD' => 'Moldova',
					'MC' => 'Monaco',
					'MN' => 'Mongolia',
					'ME' => 'Montenegro',
					'MS' => 'Montserrat',
					'MA' => 'Morocco',
					'MZ' => 'Mozambique',
					'MM' => 'Myanmar',
					'NA' => 'Namibia',
					'NR' => 'Nauru',
					'NP' => 'Nepal',
					'NL' => 'Netherlands',
					'AN' => 'Netherlands Antilles',
					'NC' => 'New Caledonia',
					'NZ' => 'New Zealand',
					'NI' => 'Nicaragua',
					'NE' => 'Niger',
					'NG' => 'Nigeria',
					'NU' => 'Niue',
					'NF' => 'Norfolk Island',
					'MP' => 'Northern Mariana Islands',
					'NO' => 'Norway',
					'OM' => 'Oman',
					'PK' => 'Pakistan',
					'PW' => 'Palau',
					'PS' => 'Palestine',
					'PA' => 'Panama',
					'PG' => 'Papua New Guinea',
					'PY' => 'Paraguay',
					'PE' => 'Peru',
					'PH' => 'Philippines',
					'PN' => 'Pitcairn',
					'PL' => 'Poland',
					'PT' => 'Portugal',
					'PR' => 'Puerto Rico',
					'QA' => 'Qatar',
					'RE' => 'Reunion',
					'RO' => 'Romania',
					'RU' => 'Russian Federation',
					'RW' => 'Rwanda',
					'BL' => 'Saint Barthelemy',
					'SH' => 'Saint Helena',
					'KN' => 'Saint Kitts and Nevis',
					'LC' => 'Saint Lucia',
					'MF' => 'Saint Martin (French part)',
					'PM' => 'Saint Pierre and Miquelon',
					'VC' => 'Saint Vincent and the Grenadines',
					'WS' => 'Samoa',
					'SM' => 'San Marino',
					'ST' => 'Sao Tome and Principe',
					'SA' => 'Saudi Arabia',
					'SN' => 'Senegal',
					'RS' => 'Serbia',
					'SC' => 'Seychelles',
					'SL' => 'Sierra Leone',
					'SG' => 'Singapore',
					'SK' => 'Slovakia',
					'SI' => 'Slovenia',
					'SB' => 'Solomon Islands',
					'SO' => 'Somalia',
					'ZA' => 'South Africa',
					'GS' => 'South Georgia and the South Sandwich Islands',
					'ES' => 'Spain',
					'LK' => 'Sri Lanka',
					'SD' => 'Sudan',
					'SR' => 'Suriname',
					'SJ' => 'Svalbard and Jan Mayen',
					'SZ' => 'Swaziland',
					'SE' => 'Sweden',
					'CH' => 'Switzerland',
					'SY' => 'Syrian Arab Republic',
					'TW' => 'Taiwan',
					'TJ' => 'Tajikistan',
					'TZ' => 'Tanzania',
					'TH' => 'Thailand',
					'TL' => 'Timor-Leste',
					'TG' => 'Togo',
					'TK' => 'Tokelau',
					'TO' => 'Tonga',
					'TT' => 'Trinidad and Tobago',
					'TN' => 'Tunisia',
					'TR' => 'Turkey',
					'TM' => 'Turkmenistan',
					'TC' => 'Turks and Caicos Islands',
					'TV' => 'Tuvalu',
					'UG' => 'Uganda',
					'UA' => 'Ukraine',
					'AE' => 'United Arab Emirates',
					'GB' => 'United Kingdom',
					'US' => 'United States',
					'UM' => 'United States Minor Outlying Islands',
					'UY' => 'Uruguay',
					'UZ' => 'Uzbekistan',
					'VU' => 'Vanuatu',
					'VE' => 'Venezuela',
					'VN' => 'Viet Nam',
					'VG' => 'Virgin Islands, British',
					'VI' => 'Virgin Islands, U.S.',
					'WF' => 'Wallis and Futuna',
					'EH' => 'Western Sahara',
					'YE' => 'Yemen',
					'ZM' => 'Zambia',
					'ZW' => 'Zimbabwe'
				);
				break;
				
				case 'age':
				
				$array = array (
				  '0'  => '',
				  '15'  => '15',
				  '16'  => '16',
				  '17'  => '17',
				  '18'  => '18',
				  '19'  => '19',
				  '20'  => '20',
				  '21'  => '21',
				  '22'  => '22',
				  '23'  => '23',
				  '24'  => '24',
				  '25'  => '25',
				  '26'  => '26',
				  '27'  => '27',
				  '28'  => '28',
				  '29'  => '29',
				  '30'  => '30',
				  '31'  => '31',
				  '32'  => '32',
				  '33'  => '33',
				  '34'  => '34',
				  '35'  => '35',
				  '36'  => '36',
				  '37'  => '37',
				  '38'  => '38',
				  '39'  => '39',
				  '40'  => '40',
				  '41'  => '41',
				  '42'  => '42',
				  '43'  => '43',
				  '44'  => '44',
				  '45'  => '45',
				  '46'  => '46',
				  '47'  => '47',
				  '48'  => '48',
				  '49'  => '49',
				  '50'  => '50',	
				  
				  '51'  => '51',
				  '52'  => '52',
				  '53'  => '53',
				  '54'  => '54',
				  '55'  => '55',
				  '56'  => '56',
				  '57'  => '57',
				  '58'  => '58',
				  '59'  => '59',
				  '60'  => '60',
				  '61'  => '61',
				  '62'  => '62',
				  '63'  => '63',
				  '64'  => '64',
				  '65'  => '65',
				  '66'  => '66',
				  '67'  => '67',
				  '68'  => '68',
				  '69'  => '69',
				  '70'  => '70',
				  
				  '71'  => '71',
				  '72'  => '72',
				  '73'  => '73',
				  '74'  => '74',
				  '75'  => '75',
				  '76'  => '76',
				  '77'  => '77',
				  '78'  => '78',
				  '79'  => '79',
				  '80'  => '80',
				  
				  '81'  => '81',
				  '82'  => '82',
				  '83'  => '83',
				  '84'  => '84',
				  '85'  => '85',
				  '86'  => '86',
				  '87'  => '87',
				  '88'  => '88',
				  '89'  => '89',
				   '90'  => '90',
				  '91'  => '91',
				  '92'  => '92',
				  '93'  => '93',
				  '94'  => '94',
				  '95'  => '95',
				  '96'  => '96',
				  '97'  => '97',
				  '98'  => '98',
				  '99'  => '99',
				  '100'  => '100',
				  
				  
				   	  
				  
				  );
				
				
				break;
				
		}
		
		return $array;
	
	}
	
	function get_select_value($from,$to){
		$html ='';
		
		for ($x = $from; $x <= $to; $x++){
   		 	$html .= '<option value="'.$x.'">'.$x.'</option>';
		} 
		
		return $html;		
	
	}
	
	/**
	 * Convert number of seconds into hours, minutes and seconds
	 * and return an array containing those values
	 *
	 * @param integer $seconds Number of seconds to parse
	 * @return array
	 */
	function secondsToTime($seconds)
	{
		// extract hours
		$hours = floor($seconds / (60 * 60));
	
		// extract minutes
		$divisor_for_minutes = $seconds % (60 * 60);
		$minutes = floor($divisor_for_minutes / 60);
	
		// extract the remaining seconds
		$divisor_for_seconds = $divisor_for_minutes % 60;
		$seconds = ceil($divisor_for_seconds);
	
		// return the final array
		$obj = array(
			"h" => (int) $hours,
			"m" => (int) $minutes,
			"s" => (int) $seconds,
		);
		return $obj;
	}
	
}
?>