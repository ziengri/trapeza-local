

function transliterate(word, isUrl) {

    var tr = {"А": "A", "а": "a", "Б": "B", "б": "b",
        "В": "V", "в": "v", "Г": "G", "г": "g",
        "Д": "D", "д": "d", "Е": "E", "е": "e",
        "Ё": "E", "ё": "e", "Ж": "Zh", "ж": "zh",
        "З": "Z", "з": "z", "И": "I", "и": "i",
        "Й": "Y", "й": "y", "КС": "X", "кс": "x",
        "К": "K", "к": "k", "Л": "L", "л": "l",
        "М": "M", "м": "m", "Н": "N", "н": "n",
        "О": "O", "о": "o", "П": "P", "п": "p",
        "Р": "R", "р": "r", "С": "S", "с": "s",
        "Т": "T", "т": "t", "У": "U", "у": "u",
        "Ф": "F", "ф": "f", "Х": "H", "х": "h",
        "Ц": "Ts", "ц": "ts", "Ч": "Ch", "ч": "ch",
        "Ш": "Sh", "ш": "sh", "Щ": "Sch", "щ": "sch",
        "Ы": "Y", "ы": "y", "Ь": "'", "ь": "'",
        "Э": "E", "э": "e", "Ъ": "'", "ъ": "'",
        "Ю": "Yu", "ю": "yu", "Я": "Ya", "я": "ya"};


    var result = "";

    result = word.split('').map(function(char) {
        return tr[char] || char;
    }).join("");

    if (isUrl == 'yes') {
        result = result
                .toLowerCase() // change everything to lowercase
                .replace(/^\s+|\s+$/g, "") // trim leading and trailing spaces
                .replace(/[_|\s]+/g, "-") // change all spaces and underscores to a hyphen
                .replace(/[^a-z\u0400-\u04FF0-9-]+/g, "") // remove all non-cyrillic, non-numeric characters except the hyphen
                .replace(/[-]+/g, "-") // replace multiple instances of the hyphen with a single instance
                .replace(/^-+|-+$/g, "") // trim leading and trailing hyphens
                .replace(/[-]+/g, "-")
    }
    return result;
}

function InitTransliterate() {
    if ($nc("[data-type='transliterate']").length) {
        $nc.each($nc("[data-type='transliterate']"), function() {
            $nc(this).after('<span class="nc-transliterate-action nc-icon nc--refresh" title="Транслитерация названия"></span>');
            $nc(this).next('.nc-transliterate-action').click(function(e) {
                e.preventDefault();
                elemName = $nc(this).prev().attr('data-from');
                isUrl = $nc(this).prev().attr('data-is-url');
                $nc(this).prev().val(transliterate($nc('[name="' + elemName + '"]').val(), isUrl));
            });
        });
    }
}
$nc(document).ready(function() {
    InitTransliterate();
});