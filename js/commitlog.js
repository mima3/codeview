window.addEventListener("load", function(event) {
    new Vue({
        el: "#commitlog",
        delimiters: ['${', '}'],
        data: function() {
            return {
                errors : [],
                isProcessing : false,
                expands : {}
            }
        },
        computed : {

        },
        components: {
            'bulmadialog-component': BulmaDialog,
        },        
        methods: {
            expand : function (commitId) {
                // this.expands[commitId] = true だとVueJSが認識しない
                this.$set(this.expands, commitId, true);
            },
            collapse : function (commitId) {
                this.$set(this.expands, commitId, false);
            },
            update : function (url) {
                console.log('update ' + url);
                this.isProcessing = true;
                const self = this;
                axios.post(
                    url
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
                    if (res.data.updated) {
                        window.location.reload();
                    }
                    self.isProcessing = false;
                }).catch(function(err) {
                    self.errors.push(err);
                    self.isProcessing = false;
                });                
            }
        },
        created : function() {
            document.getElementById ('commitlog').style.display = ''; 
        }   
    });
});
