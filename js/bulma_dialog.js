/**
 * bulma + vue.jsでダイアログを表示します。
 * html: vue.jsの管理下に以下を追加します
 *   <bulmadialog-component ref="dialog"></bulmadialog-component>
 * js:Vue.jsを作成するときにコンポーネントを追加する
 *  components: {
      'bulmadialog-component': BulmaDialog,
    },
 * js:Vue.jsの親のメソッドにて以下を実行
 *  this.$refs.dialog.showDialog({
        title:'わっふるる',
        //contents:'わっふるぼでぃ０\nsadfasfd',
        html : 'あたえたｔ<br>awrawtあたえたｔ<br>',
        buttons : [
          {
            caption : 'はい',
            callback : function () {
              console.log('はい');
            }
          },
          {
            caption : 'いいえ',
            callback : function () {
              console.log('いいえ');
            }
          }
        ]
      });
 */
// eslint-disable-next-line no-unused-vars
const BulmaDialog = {
  /* eslint-disable max-len */
  template: (function() {/*
      <div v-bind:class="{ 'is-active': isShow }" class="modal">
        <div class="modal-background"></div>
          <div class="modal-card">
              <header class="modal-card-head">
                  <p class="modal-card-title">{{data.title}}</p>
              </header>
              <div >
              </div>
              <section v-if="data.html" v-html="data.html" class="modal-card-body"></section>
              <section v-else class="modal-card-body">{{data.contents}}</section>
              <footer class="modal-card-foot"  style="justify-content: flex-end;">
                  <button v-for="btnObj in data.buttons" type="button" class="button" @click="btnObj.callback(); isShow = false;">{{btnObj.caption}}</button>
              </footer>
          </div>
      </div>
      </div>
    */}).toString().match(/\/\*([^]*)\*\//)[1],
  /* eslint-enable */
  data: function() {
    return {
      isShow: false,
      data: {
        title: '',
        body: '',
        html: '',
        buttons: [],
      },
    };
  },
  methods: {
    showDialog: function(data) {
      this.isShow = true;
      this.data.title = data.title;
      this.data.contents = data.contents;
      this.data.html = data.html;
      this.data.buttons = data.buttons;
    },
  },
};
