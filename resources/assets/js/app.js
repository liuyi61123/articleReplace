require('./bootstrap');

window.Vue = require('vue');
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI);

// 页面
Vue.component('article-param-table', require('./views/article/ParamTable.vue'));//参数列表
Vue.component('article-create-edit-param', require('./views/article/CreateEditParam.vue'));
Vue.component('article-article-table', require('./views/article/ArticleTable.vue'));//文章列表
Vue.component('article-create-edit-article', require('./views/article/CreateEditArticle.vue'));//编辑和新建文章
Vue.component('article-template-table', require('./views/article/TemplateTable.vue'));//模板列表
Vue.component('article-create-edit-template', require('./views/article/CreateEditTemplate.vue'));//编辑和新建模板
Vue.component('article-create-edit-paragraph', require('./views/article/CreateEditParagraph.vue'));//编辑和新建段落
Vue.component('article-paragraph-table', require('./views/article/ParagraphTable.vue'));//段落列表

Vue.component('pseudo-original', require('./views/PseudoOriginal.vue'));//编辑和新建模板
Vue.component('baidu-url-create', require('./views/BaiduUrlCreate.vue'));//新建baidu_url
Vue.component('baidu-url-table', require('./views/BaiduUrlTable.vue'));//新建baidu_url
Vue.component('create-edit-website', require('./views/CreateEditWebsite.vue'));
Vue.component('website-table', require('./views/WebsiteTable.vue'));
Vue.component('website-category-table', require('./views/WebsiteCategoryTable.vue'));
Vue.component('create-edit-website-category', require('./views/CreateEditWebsiteCategory.vue'));
Vue.component('website-push-table', require('./views/WebsitePushTable.vue'));
Vue.component('manual-website-push', require('./views/ManualWebsitePush.vue'));
Vue.component('website-push', require('./views/WebsitePush.vue'));

//组件
// Vue.component('upload-image', require('./views/components/UploadImage.vue'));//编辑和新建模板

const app = new Vue({
    el: '#app'
});
