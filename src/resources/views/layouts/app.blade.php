<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dispatch, telling</title>
    <!-- Fonts -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="//fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <!-- Injected scripts -->
    @yield('scripts', '')

            <!-- Injected styles -->
    @yield('styles', '')
            <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <stype>

    </stype>
</head>
<body>
<div class="container">
    @yield('content')
</div>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.14/vue.js"></script>
<script>

    // define
    var vue_comment = Vue.extend({
        props: [ 'response', 'action', 'body', 'ticket_id', 'user_id', '_token'],
        methods:{
            makeRequest: function (e) {
                var self = this;
                console.log(this.$data);
                e.preventDefault();
                this.request(e.target.action,
                        (this.$data)
                        , function(responseArea){
                            var response = $('#response');
                            response.removeClass (function (index, css) {
                                return (css.match (/\balert-.*\s/g) || []).join(' ');
                            }).addClass('alert-success');

                            self.body = '';
                        }, function(responseArea){
                            var response = $('#response');
                            response.removeClass (function (index, css) {
                                return (css.match (/\balert-.*\s/g) || []).join(' ');
                            }).addClass('alert-warning');

                        }, function(responseArea){
                            var response = $('#response');
                            response.removeClass (function (index, css) {
                                return (css.match (/\balert-.*\s/g) || []).join(' ');
                            }).addClass('alert-danger');
                        });
            },
            close: function (e) {
                this.response = '';
            },
            request: function (url, data, success, fail, nochange) {
                var xmlhttp = new XMLHttpRequest(),
                        self = this;   // new HttpRequest instance
                xmlhttp.open("post", url);

                xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                xmlhttp.setRequestHeader("X-Requested-With", 'XMLHttpRequest');
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState == 4) {
                        var response = JSON.parse(xmlhttp.responseText);
                        self.response = 'Comment posted successfully';
                        var respArea = document.getElementById('response');
                        if (!respArea.classList.contains('alert)')) {
                            respArea.className = 'alert ';
                        }
                        switch (response.code) {
                            case 200:
                            case 202:
                                success(respArea);
                                break;
                            case 205:
                                nochange(respArea);
                                break;
                            default:
                                fail(respArea);
                        }
                    }
                };
                xmlhttp.send(JSON.stringify(data));
            }
        },
        template: '<div id ="response" v-show="response" class="alert">{{ response }}<div class="close" @click="close">&times;</div></div>\
    <form :action="action" method="POST"  class="make-comment" style="background:white" @submit.prevent="makeRequest">\
        <input type="text" name="fake_body" style="display:none;">\
        <input type="text" name="body" placeholder="Comment on this" v-model="body" class="form-control input-sm" autocomplete="off" style="box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);">\
        <input type="hidden" name="ticket_id" :value="ticket_id" v-model="ticket_id">\
        <input type="hidden" name="_token"    :value="_token"    v-model="_token">\
        <input type="hidden" name="user_id"   :value="user_id"   v-model="user_id">\
        <input type="hidden" name="_method"   value="POST"> \
        <input type="submit" style="position:absolute;top:-1000px; left:-10000px;">\
    </form>'
    });
    var vue_show_more = Vue.extend({
        props: ['data'],
        data: function() {
            return {
                show: false,
                count:0
            };
        },
        methods:{
            toggleShow:function(){
                this.show = !this.show;
            }
        },
        template: '<ul class="show-more" >\
        <li v-for="info in data" v-show="($index > 4 ?show:true)">{{info.name}}</li>\
        </ul>\
        <a href="#!" class="more-link" v-on:click.prevent="toggleShow" v-if="data.length > 5">{{ !show ? data.length - 5 + " more..." : "less..."}} </a>'
    });
    Vue.component('show-more-list', vue_show_more);
    Vue.component('ticket-make-comment', vue_comment);
    new Vue({
        el:'body'
    });
</script>
</body>
</html>