

function transliterate(word, isUrl) {

    var tr = {"�": "A", "�": "a", "�": "B", "�": "b",
        "�": "V", "�": "v", "�": "G", "�": "g",
        "�": "D", "�": "d", "�": "E", "�": "e",
        "�": "E", "�": "e", "�": "Zh", "�": "zh",
        "�": "Z", "�": "z", "�": "I", "�": "i",
        "�": "Y", "�": "y", "��": "X", "��": "x",
        "�": "K", "�": "k", "�": "L", "�": "l",
        "�": "M", "�": "m", "�": "N", "�": "n",
        "�": "O", "�": "o", "�": "P", "�": "p",
        "�": "R", "�": "r", "�": "S", "�": "s",
        "�": "T", "�": "t", "�": "U", "�": "u",
        "�": "F", "�": "f", "�": "H", "�": "h",
        "�": "Ts", "�": "ts", "�": "Ch", "�": "ch",
        "�": "Sh", "�": "sh", "�": "Sch", "�": "sch",
        "�": "Y", "�": "y", "�": "'", "�": "'",
        "�": "E", "�": "e", "�": "'", "�": "'",
        "�": "Yu", "�": "yu", "�": "Ya", "�": "ya"};


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
            $nc(this).after('<span class="nc-transliterate-action nc-icon nc--refresh" title="�������������� ��������"></span>');
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