@extends('layouts.master')

@section('content')

    <section id="recent-works">
            <div class="row">
                <div class="features">
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        
                        {{ Form::open(array('url' => 'profiles', 'files'=> true)) }}
                        <!-- Date Input -->
                                
                             <div class="form-group">
                             {{ Form::label('first_name', 'First Name &nbsp; (Optional)') }}
                             {{ Form::text('first_name', '', array('class'=>'form-control')) }}
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('last_name', 'Last Name &nbsp; (Optional)') }}
                             {{ Form::text('last_name', '', array('class'=>'form-control')) }}
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('gender', 'Gender') }}
                             {{ Form::select('gender', array('male' => 'Male', 'female' => 'Female'), '', array('class' => 'form-control')) }}
                             </div>
                             
                           <div class="form-group">
                        <label for="birthday">Birthday:</label>
                        <label class="light_gray_font" for="month">Month:</label> <!-- <input type="text" name="month" id="month"> -->
                        <select name="birth_month">
                        <option value="01" >January</option>
                        <option value="02" >February</option>
                        <option value="03" >March</option>
                        <option value="04" >April</option>
                        <option value="05" >May</option>
                        <option value="06" >June</option>
                        <option value="07" >July</option>
                        <option value="08" >August</option>
                        <option value="09" >September</option>
                        <option value="10" >October</option>
                        <option value="11" >November</option>
                        <option value="12" >December</option>
                        </select>
                                
                        <label class="light_gray_font" for="date">Date:</label> <!-- <input type="text" name="date" id="date"> -->
                        <select name="birth_date">
                        <?php
                        for ($i=1; $i<=31; $i++) {
                          ?>
                          <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                          <?php
                        }
                        ?>
                        </select>
                                
                        <label class="light_gray_font" for="year">Year:</label>
                        <select name="birth_year">
                        <?php
                        //Starting age should be 13, therefore use ( current year - 13 ) to determine the youngest possible birthdate
                        $youngest_y = (date('Y')-13);
                        for($i= $youngest_y; $i>=1906; $i--) {
                          ?>
                          <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                          <?php
                        }
                        ?>
                        </select>
                        </div>
                           
                             <div class="form-group">
                             {{ Form::label('country', 'Country') }}
                             {{ Form::select('country', array(
                                 "AF" => "Afghanistan",
                                 "AL" => "Albania",
                                 "DZ" => "Algeria",
                                 "AS" => "American Samoa",
                                 "AD" => "Andorra",
                                 "AO" => "Angola",
                                 "AI" => "Anguilla",
                                 "AQ" => "Antarctica",
                                 "AG" => "Antigua And Barbuda",
                                 "AR" => "Argentina",
                                 "AM" => "Armenia",
                                 "AW" => "Aruba",
                                 "AU" => "Australia",
                                 "AT" => "Austria",
                                 "AZ" => "Azerbaijan",
                                 "BS" => "Bahamas",
                                 "BH" => "Bahrain",
                                 "BD" => "Bangladesh",
                                 "BB" => "Barbados",
                                 "BY" => "Belarus",
                                 "BE" => "Belgium",
                                 "BZ" => "Belize",
                                 "BJ" => "Benin",
                                 "BM" => "Bermuda",
                                 "BT" => "Bhutan",
                                 "BO" => "Bolivia",
                                 "BA" => "Bosnia And Herzegowina",
                                 "BW" => "Botswana",
                                 "BV" => "Bouvet Island",
                                 "BR" => "Brazil",
                                 "IO" => "British Indian Ocean Territory",
                                 "BN" => "Brunei Darussalam",
                                 "BG" => "Bulgaria",
                                 "BF" => "Burkina Faso",
                                 "BI" => "Burundi",
                                 "KH" => "Cambodia",
                                 "CM" => "Cameroon",
                                 "CA" => "Canada",
                                 "CV" => "Cape Verde",
                                 "KY" => "Cayman Islands",
                                 "CF" => "Central African Republic",
                                 "TD" => "Chad",
                                 "CL" => "Chile",
                                 "CN" => "China",
                                 "CX" => "Christmas Island",
                                 "CC" => "Cocos (Keeling) Islands",
                                 "CO" => "Colombia",
                                 "KM" => "Comoros",
                                 "CG" => "Congo",
                                 "CD" => "Congo, The Democratic Republic Of The",
                                 "CK" => "Cook Islands",
                                 "CR" => "Costa Rica",
                                 "CI" => "Cote D'Ivoire",
                                 "HR" => "Croatia (Local Name: Hrvatska)",
                                 "CU" => "Cuba",
                                 "CY" => "Cyprus",
                                 "CZ" => "Czech Republic",
                                 "DK" => "Denmark",
                                 "DJ" => "Djibouti",
                                 "DM" => "Dominica",
                                 "DO" => "Dominican Republic",
                                 "TP" => "East Timor",
                                 "EC" => "Ecuador",
                                 "EG" => "Egypt",
                                 "SV" => "El Salvador",
                                 "GQ" => "Equatorial Guinea",
                                 "ER" => "Eritrea",
                                 "EE" => "Estonia",
                                 "ET" => "Ethiopia",
                                 "FK" => "Falkland Islands (Malvinas)",
                                 "FO" => "Faroe Islands",
                                 "FJ" => "Fiji",
                                 "FI" => "Finland",
                                 "FR" => "France",
                                 "FX" => "France, Metropolitan",
                                 "GF" => "French Guiana",
                                 "PF" => "French Polynesia",
                                 "TF" => "French Southern Territories",
                                 "GA" => "Gabon",
                                 "GM" => "Gambia",
                                 "GE" => "Georgia",
                                 "DE" => "Germany",
                                 "GH" => "Ghana",
                                 "GI" => "Gibraltar",
                                 "GR" => "Greece",
                                 "GL" => "Greenland",
                                 "GD" => "Grenada",
                                 "GP" => "Guadeloupe",
                                 "GU" => "Guam",
                                 "GT" => "Guatemala",
                                 "GN" => "Guinea",
                                 "GW" => "Guinea-Bissau",
                                 "GY" => "Guyana",
                                 "HT" => "Haiti",
                                 "HM" => "Heard And Mc Donald Islands",
                                 "VA" => "Holy See (Vatican City State)",
                                 "HN" => "Honduras",
                                 "HK" => "Hong Kong",
                                 "HU" => "Hungary",
                                 "IS" => "Iceland",
                                 "IN" => "India",
                                 "ID" => "Indonesia",
                                 "IR" => "Iran (Islamic Republic Of)",
                                 "IQ" => "Iraq",
                                 "IE" => "Ireland",
                                 "IL" => "Israel",
                                 "IT" => "Italy",
                                 "JM" => "Jamaica",
                                 "JP" => "Japan",
                                 "JO" => "Jordan",
                                 "KZ" => "Kazakhstan",
                                 "KE" => "Kenya",
                                 "KI" => "Kiribati",
                                 "KP" => "Korea, Democratic People's Republic Of",
                                 "KR" => "Korea, Republic Of",
                                 "KW" => "Kuwait",
                                 "KG" => "Kyrgyzstan",
                                 "LA" => "Lao People's Democratic Republic",
                                 "LV" => "Latvia",
                                 "LB" => "Lebanon",
                                 "LS" => "Lesotho",
                                 "LR" => "Liberia",
                                 "LY" => "Libyan Arab Jamahiriya",
                                 "LI" => "Liechtenstein",
                                 "LT" => "Lithuania",
                                 "LU" => "Luxembourg",
                                 "MO" => "Macau",
                                 "MK" => "Macedonia, Former Yugoslav Republic Of",
                                 "MG" => "Madagascar",
                                 "MW" => "Malawi",
                                 "MY" => "Malaysia",
                                 "MV" => "Maldives",
                                 "ML" => "Mali",
                                 "MT" => "Malta",
                                 "MH" => "Marshall Islands",
                                 "MQ" => "Martinique",
                                 "MR" => "Mauritania",
                                 "MU" => "Mauritius",
                                 "YT" => "Mayotte",
                                 "MX" => "Mexico",
                                 "FM" => "Micronesia, Federated States Of",
                                 "MD" => "Moldova, Republic Of",
                                 "MC" => "Monaco",
                                 "MN" => "Mongolia",
                                 "MS" => "Montserrat",
                                 "MA" => "Morocco",
                                 "MZ" => "Mozambique",
                                 "MM" => "Myanmar",
                                 "NA" => "Namibia",
                                 "NR" => "Nauru",
                                 "NP" => "Nepal",
                                 "NL" => "Netherlands",
                                 "AN" => "Netherlands Antilles",
                                 "NC" => "New Caledonia",
                                 "NZ" => "New Zealand",
                                 "NI" => "Nicaragua",
                                 "NE" => "Niger",
                                 "NG" => "Nigeria",
                                 "NU" => "Niue",
                                 "NF" => "Norfolk Island",
                                 "MP" => "Northern Mariana Islands",
                                 "NO" => "Norway",
                                 "OM" => "Oman",
                                 "PK" => "Pakistan",
                                 "PW" => "Palau",
                                 "PA" => "Panama",
                                 "PG" => "Papua New Guinea",
                                 "PY" => "Paraguay",
                                 "PE" => "Peru",
                                 "PH" => "Philippines",
                                 "PN" => "Pitcairn",
                                 "PL" => "Poland",
                                 "PT" => "Portugal",
                                 "PR" => "Puerto Rico",
                                 "QA" => "Qatar",
                                 "RE" => "Reunion",
                                 "RO" => "Romania",
                                 "RU" => "Russian Federation",
                                 "RW" => "Rwanda",
                                 "KN" => "Saint Kitts And Nevis",
                                 "LC" => "Saint Lucia",
                                 "VC" => "Saint Vincent And The Grenadines",
                                 "WS" => "Samoa",
                                 "SM" => "San Marino",
                                 "ST" => "Sao Tome And Principe",
                                 "SA" => "Saudi Arabia",
                                 "SN" => "Senegal",
                                 "SC" => "Seychelles",
                                 "SL" => "Sierra Leone",
                                 "SG" => "Singapore",
                                 "SK" => "Slovakia (Slovak Republic)",
                                 "SI" => "Slovenia",
                                 "SB" => "Solomon Islands",
                                 "SO" => "Somalia",
                                 "ZA" => "South Africa",
                                 "GS" => "South Georgia, South Sandwich Islands",
                                 "ES" => "Spain",
                                 "LK" => "Sri Lanka",
                                 "SH" => "St. Helena",
                                 "PM" => "St. Pierre And Miquelon",
                                 "SD" => "Sudan",
                                 "SR" => "Suriname",
                                 "SJ" => "Svalbard And Jan Mayen Islands",
                                 "SZ" => "Swaziland",
                                 "SE" => "Sweden",
                                 "CH" => "Switzerland",
                                 "SY" => "Syrian Arab Republic",
                                 "TW" => "Taiwan",
                                 "TJ" => "Tajikistan",
                                 "TZ" => "Tanzania, United Republic Of",
                                 "TH" => "Thailand",
                                 "TG" => "Togo",
                                 "TK" => "Tokelau",
                                 "TO" => "Tonga",
                                 "TT" => "Trinidad And Tobago",
                                 "TN" => "Tunisia",
                                 "TR" => "Turkey",
                                 "TM" => "Turkmenistan",
                                 "TC" => "Turks And Caicos Islands",
                                 "TV" => "Tuvalu",
                                 "UG" => "Uganda",
                                 "UA" => "Ukraine",
                                 "AE" => "United Arab Emirates",
                                 "GB" => "United Kingdom",
                                 "US" => "United States",
                                 "UM" => "United States Minor Outlying Islands",
                                 "UY" => "Uruguay",
                                 "UZ" => "Uzbekistan",
                                 "VU" => "Vanuatu",
                                 "VE" => "Venezuela",
                                 "VN" => "Viet Nam",
                                 "VG" => "Virgin Islands (British)",
                                 "VI" => "Virgin Islands (U.S.)",
                                 "WF" => "Wallis And Futuna Islands",
                                 "EH" => "Western Sahara",
                                 "YE" => "Yemen",
                                 "YU" => "Yugoslavia",
                                 "ZM" => "Zambia",
                                 "ZW" => "Zimbabwe"
                                 ), "", array('class' => 'form-control')) }}
                             </div>
                             
                             
                             <div class="form-group">
                             {{ Form::label('about', 'About Me') }}
                             {{ Form::textarea('about', '', array('class'=>'form-control chr-count', 'rows'=>'5', 'maxlength'=>'200')) }}
                             </div>
                             
                             <div class="form-group">
                            {{ Form::label('image', 'Add Profile Image &nbsp; (Optional)') }}
                            {{ Form::file('image') }}
                             </div>
                             
                             <br />
                             
                             {{ Form::submit('Save', array('class'=>'btn btn-default')) }}
                             <span class="countdown"></span>
                        
                        {{ Form::close() }}
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- right -->
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
    </section>

@stop
