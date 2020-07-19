window.addEventListener("load", function(event) {
    new Vue({
        el: "#container_repository",
        delimiters: ['${', '}'],
        data: function() {
            return {
                errors : [],
                isProcessing : false
            }
        },
        components: {
            'bulmadialog-component': BulmaDialog,
        },        
        methods: {
            add : function () {
                this.isProcessing = true;
                window.location.href = 'addpage';
            },
            deleteRepository : function(url, name, elem) {
                let self = this;
                this.$refs.dialog.showDialog({
                    title: '確認',
                    contents: name + 'を本当に削除してよろしいですか?',
                    buttons : [
                        {
                            caption : 'はい',
                            callback : function () {
                                console.log('削除ボタン' + url);
                                self.isProcessing = true;
                                axios.post(
                                    url
                                ).then(function(res) {
                                    if (res.headers['content-type'] !== 'application/json') {
                                        self.isProcessing = false;
                                        self.errors.push(res.data);
                                        return;
                                    }
                                    if (res.data.errors) {
                                        self.isProcessing = false;
                                        self.errors.push(res.data.errors);
                                        return;
                                    }
                                    console.log(res);
                                    window.location.href = '.';
                                }).catch(function(err) {
                                    self.errors.push(err);
                                    window.scrollTo(0,0);
                                    self.isProcessing = false;
                                });
                            }
                        },
                        {
                            caption : 'いいえ',
                            callback : function () {
                                console.log('いいえ');
                            }
                        },
                    ]
                  });
                return;
            }
        },
        created () {
            document.getElementById ('container_repository').style.display = ''; 
        }   
    });
});
