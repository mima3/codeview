let vue;
window.addEventListener("load", function(event) {
    vue = new Vue({
        el: "#container_repository_add",
        delimiters: ['${', '}'],
        data: function() {
            return {
                add_info : {
                    remote : 'https://github.com/mima3/railway_location.git',
                    name : 'ほげほげ',
                    branch : 'master'
                },
                errors : [],
                isProcessing : false
            }
        },
        methods: {
            add : function() {
                console.log('追加ボタン');
                this.errors = [];
                if (!this.add_info.name) {
                    this.errors.push('Nameに値を入力してください。');
                }
                if (!this.add_info.remote) {
                    this.errors.push('Remoteに値を入力してください。');
                }
                if (!this.add_info.branch) {
                    this.errors.push('Branchに値を入力してください。');
                }
                if (this.errors.length > 0) {
                    return;
                }
                let self = this;
                self.isProcessing = true;
                axios.post(
                    'add',
                    this.add_info
                ).then(function(res) {
                    if (res.headers['content-type'] !== 'application/json') {
                        self.errors.push(res.data);
                        self.isProcessing = false;
                        return;
                    }
                    if (res.data.errors) {
                        self.errors.push(res.data.errors);
                        self.isProcessing = false;
                        return;
                    }
                    console.log(res.data);
                    window.location.href = '.';
                }).catch(function(err) {
                    self.errors.push(err);
                    self.isProcessing = false;
                });
            }, 
            cancel : function() {
                this.isProcessing = true;
                window.location.href = '.';
            }
        },
        created () {
            console.log('hoge is: ' + this.hoge);
            document.getElementById ('container_repository_add').style.display = ''; 
        }        
    });
});
