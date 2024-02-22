<?php
/**
 * This file has been @generated by a phing task by {@link BuildMetadataPHPFromXml}.
 * See [README.md](README.md#generating-data) for more information.
 *
 * Pull requests changing data in these files will not be accepted. See the
 * [FAQ in the README](README.md#problems-with-invalid-numbers] on how to make
 * metadata changes.
 *
 * Do not modify this file directly!
 */


return array (
  'generalDesc' => 
  array (
    'NationalNumberPattern' => '[1-9]\\d{2,4}',
    'PossibleLength' => 
    array (
      0 => 3,
      1 => 4,
      2 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '1(?:1(?:6|9\\d)|5(?:01|2[127]|6(?:29|6[67])))',
    'ExampleNumber' => '1501',
    'PossibleLength' => 
    array (
      0 => 4,
      1 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'premiumRate' => 
  array (
    'NationalNumberPattern' => '909\\d{2}',
    'ExampleNumber' => '90912',
    'PossibleLength' => 
    array (
      0 => 5,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'emergency' => 
  array (
    'NationalNumberPattern' => '112|114|999',
    'ExampleNumber' => '999',
    'PossibleLength' => 
    array (
      0 => 3,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'shortCode' => 
  array (
    'NationalNumberPattern' => '1(?:0(?:[07-9]|1[125]|400)|1(?:[02456]|9[0-579])|2[123]|3[01]|5(?:[01][01]|2[0-24-79]|33|40|55|6(?:29|6[67]))|6[035]\\d{2}|[78]\\d|9(?:[02-9]\\d{2}|19))|(?:2[0-79]|3[0-29]|4[0-4])\\d{3}|5(?:[0-7]\\d|99)\\d{2}|(?:6[2357]|7[0-29])\\d{3}|8(?:[0-9]\\d{3}|988)|9(?:09\\d{2}|99)',
    'ExampleNumber' => '116',
    'PossibleLength' => 
    array (
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'standardRate' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'carrierSpecific' => 
  array (
    'NationalNumberPattern' => '1(?:0400|3[01]|5(?:1[01]|2[25])|6[35]\\d{2})|(?:2[0-79]|3[0-29]|4[0-4])\\d{3}|5(?:[0-7]\\d|99)\\d{2}|(?:6[2357]|7[0-29])\\d{3}|8(?:988|[0-9]\\d{3})|909\\d{2}',
    'ExampleNumber' => '90912',
    'PossibleLength' => 
    array (
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'KE',
  'countryCode' => 0,
  'internationalPrefix' => '',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' => 
  array (
  ),
  'intlNumberFormat' => 
  array (
  ),
  'mainCountryForCode' => false,
  'leadingZeroPossible' => false,
  'mobileNumberPortableRegion' => false,
);