require('./bootstrap');

window.Vue = require('vue');
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI);

// 页面
Vue.component('article-table', require('./views/ArticleTable.vue'));//文章列表
Vue.component('create-edit-article', require('./views/CreateEditArticle.vue'));//编辑和新建文章
Vue.component('template-table', require('./views/TemplateTable.vue'));//模板列表
Vue.component('create-edit-template', require('./views/CreateEditTemplate.vue'));//编辑和新建模板

//组件
// Vue.component('upload-image', require('./views/components/UploadImage.vue'));//编辑和新建模板

const app = new Vue({
    el: '#app'
});
