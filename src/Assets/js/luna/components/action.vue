<template>
  <div>
    <slot :fire="fire"></slot>
    <b-modal ref="form" :title="action.title" v-if="action.fields && openForm" hide-footer>
      <ajax-form ref="ajaxForm" :custom-submit="true" @submit="submitAndReset">
        <div class="input-fields">
          <template v-for="(field, index) in action.fields">
            <div class="py-2">
              <component :is="'luna-type-' + field.type"
                         :field="field"
                         :display-type="3"
                         :values="values"
                         frame-class="col-md-9"></component>
            </div>
          </template>
          <div class="mt-3 pt-3 px-3 border-top text-right" style="margin-left: -1rem; margin-right: -1rem">
            <button class="btn btn-primary" @click="$refs.ajaxForm.submit()" :disabled="isLoading">
              <span>تایید</span>
            </button>
          </div>
        </div>
      </ajax-form>
    </b-modal>
  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  props: {
    resource: {
      required: true,
      type: String,
    },
    idx: {
      require: true,
      type: Number
    },
    action: {
      required: true,
      type: Object
    },
    models: {
      required: true,
      type: Array,
    }
  },
  data() {
    return {
      openForm: false,
      values: {},
    }
  },
  methods: {
    ...mapActions([
      'error',
      'si',
    ]),
    fire() {
      if (this.action.fields) {
        this.openForm = true;
        this.$nextTick(() => {
          this.$refs['form'].show()
        })
      } else {
        return this.realFire()
      }
    },
    realFire() {
      return new Promise((resolve, reject) => {
        this.si({
          url: this.route('luna.resources.action', [this.resource, this.idx]),
          method: 'post',
          data: {
            ...this.values,
            models: this.models,
          },
        }).then(x => {
          this.onSuccess(x)
          resolve()
        }).catch(result => {
          if (result.hasOwnProperty('response') && result.response.status == 422) {
            if (result.response.hasOwnProperty('data')) {
              if (result.response.data.hasOwnProperty('message')){
                this.error({
                  title: 'خطا',
                  text: result.response.data.message,
                });
              }

              if (result.response.data.hasOwnProperty('errors')) {
                this.$refs.ajaxForm.setErrors(result.response.data.errors)
              }
            }
          }
        });
      })
    },
    submitAndReset() {
      this.realFire().then(() => {
        this.values = {}
        this.$refs['form'].hide()
      })
    },
    onSuccess(data) {
      if (data.action == 'message') {
        this.$swal({
          title: data.message.title,
          text: data.message.text,
          type: data.message.type,
          confirmButtonText: 'خب!',
          target: document.getElementById('app')
        }).then(() => {
          if (data.refresh) {
            window.location.reload()
          }
        });
      } else if (data.action == 'redirect') {
        if (data.redirect.new_tab) {
          let win = window.open(data.redirect.url, '_blank');
          win.focus();
        } else {
          document.location = data.redirect.url
        }
      } else if (data.action == 'download') {
        let blob = this.base64toBlob(data.download.content, data.download.mime);

        if (window.navigator.msSaveOrOpenBlob) {
          window.navigator.msSaveBlob(blob, data.download.filename);
        } else {
          let elem = window.document.createElement('a');
          elem.href = window.URL.createObjectURL(blob);
          elem.download = data.download.filename;
          document.body.appendChild(elem);
          elem.click();
          document.body.removeChild(elem);
        }
      } else if (data.action == 'refresh') {
        window.location.reload()
      }
    },
    base64toBlob(base64Data, contentType) {
      contentType = contentType || '';
      let sliceSize = 1024;
      let byteCharacters = atob(base64Data);
      let bytesLength = byteCharacters.length;
      let slicesCount = Math.ceil(bytesLength / sliceSize);
      let byteArrays = new Array(slicesCount);

      for (let sliceIndex = 0; sliceIndex < slicesCount; ++sliceIndex) {
        let begin = sliceIndex * sliceSize;
        let end = Math.min(begin + sliceSize, bytesLength);

        let bytes = new Array(end - begin);
        for (let offset = begin, i = 0; offset < end; ++i, ++offset) {
          bytes[i] = byteCharacters[offset].charCodeAt(0);
        }
        byteArrays[sliceIndex] = new Uint8Array(bytes);
      }
      return new Blob(byteArrays, {type: contentType});
    }
  },
  computed: {
    isLoading() {
      return this.$store.state.loading
    },
  }
}
</script>
