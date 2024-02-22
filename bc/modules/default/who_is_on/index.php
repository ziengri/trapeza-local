<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/bc/modules/default/who_is_on/js/vue/vue.js"></script>
    <script src="https://unpkg.com/axios@0.21.1/dist/axios.min.js"></script>
    <title>Who is use</title>
    <style>
        #box-search {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        #box-search input {
            width: 300px;
            height: 20px;
            margin-right: 10px;
        }

        #box-search button {
            width: 100px;
        }
        .item {
            border: 1px solid #82808085;
            background: #82808026;
            padding: 10px;
            margin: 2px;
        }

        .result {
            display: flex;
            flex-wrap: wrap;
        }

    </style>
</head>
<body>
    <div id="app">
        <div id="box-search">
            <input type="text" v-model="textSearch">
            <button @click="search">Поиск</button>
        </div>
        <div class="result" v-if="response.length != 0">
            <div class="item" v-for="item in response">
                <a :href="'//' + item.catalog.Domain"  target="_blank">
                    <span class='name'>{{ item.catalog.Catalogue_Name }}</span> <span class="catalogue">ID: {{ item.catalog.Catalogue_ID }}</span>
                </a>
                <br>
                <span class="value">Значения: {{ item.res }}</span>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        new Vue({
            el: '#app',
            data: {
                action: '/bc/modules/default/who_is_on/function.php',
                textSearch: '',
                response: []
            },
            methods: {
                search: function() {
                    var params = new URLSearchParams();
                    params.append('textSearch', this.textSearch);
                    axios.post(this.action, params)
                    .then((response) => {
                        this.response = response.data
                    })
                }
            }
        })
    </script>
</body>
</html>
