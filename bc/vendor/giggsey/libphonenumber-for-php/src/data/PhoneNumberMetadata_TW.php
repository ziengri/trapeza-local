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
    'NationalNumberPattern' => '2\\d{6,8}|[3-689]\\d{7,8}|7\\d{7,9}',
    'PossibleLength' => 
    array (
      0 => 7,
      1 => 8,
      2 => 9,
      3 => 10,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'fixedLine' => 
  array (
    'NationalNumberPattern' => '2(?:[235-8]\\d{7}|4\\d{6,7})|[3-8]\\d{7,8}',
    'ExampleNumber' => '221234567',
    'PossibleLength' => 
    array (
      0 => 8,
      1 => 9,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'mobile' => 
  array (
    'NationalNumberPattern' => '9\\d{8}',
    'ExampleNumber' => '912345678',
    'PossibleLength' => 
    array (
      0 => 9,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'tollFree' => 
  array (
    'NationalNumberPattern' => '800\\d{6}',
    'ExampleNumber' => '800123456',
    'PossibleLength' => 
    array (
      0 => 9,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'premiumRate' => 
  array (
    'NationalNumberPattern' => '20(?:2|[013-9]\\d{2})\\d{4}',
    'ExampleNumber' => '203123456',
    'PossibleLength' => 
    array (
      0 => 7,
      1 => 9,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'sharedCost' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'personalNumber' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'voip' => 
  array (
    'NationalNumberPattern' => '70\\d{8}',
    'ExampleNumber' => '7012345678',
    'PossibleLength' => 
    array (
      0 => 10,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'pager' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'uan' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'voicemail' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'noInternationalDialling' => 
  array (
    'PossibleLength' => 
    array (
      0 => -1,
    ),
    'PossibleLengthLocalOnly' => 
    array (
    ),
  ),
  'id' => 'TW',
  'countryCode' => 886,
  'internationalPrefix' => '0(?:0[25679]|19)',
  'nationalPrefix' => '0',
  'preferredExtnPrefix' => '#',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' => 
  array (
    0 => 
    array (
      'pattern' => '(20)(\\d)(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' => 
      array (
        0 => '202',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
    1 => 
    array (
      'pattern' => '(20)(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' => 
      array (
        0 => '20[013-9]',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
    2 => 
    array (
      'pattern' => '([2-8])(\\d{3,4})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' => 
      array (
        0 => '2[23-8]|[3-6]|[78][1-9]',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
    3 => 
    array (
      'pattern' => '([89]\\d{2})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' => 
      array (
        0 => '80|9',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
    4 => 
    array (
      'pattern' => '(70)(\\d{4})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' => 
      array (
        0 => '70',
      ),
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ),
  ),
  'intlNumberFormat' => 
  array (
  ),
  'mainCountryForCode' => false,
  'leadingZeroPossible' => false,
  'mobileNumberPortableRegion' => true,
);