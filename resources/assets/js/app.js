
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('article-table', require('./components/ArticleTable.vue'));//文章列表
Vue.component('create-edit-article', require('./components/CreateEditArticle.vue'));//编辑和新建文章
Vue.component('template-table', require('./components/TemplateTable.vue'));//模板列表
Vue.component('create-edit-template', require('./components/CreateEditTemplate.vue'));//编辑和新建模板

const app = new Vue({
    el: '#app'
});
