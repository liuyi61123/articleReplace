<template>
    <el-row>
        <el-form :model="website" :rules="rules" ref="website" label-width="80px"  v-loading="loading">
            <el-col :span="20">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="名称" prop="name">
                            <el-input v-model="website.name" placeholder="名称"></el-input>
                        </el-form-item>
                        <el-form-item label="网址" prop="url">
                            <el-input v-model="website.url" placeholder="网址"></el-input>
                        </el-form-item>
                        <el-form-item label="分类" prop="category_id">
                            <el-select v-model="website.category_id" placeholder="请选择">
                                <el-option
                                    v-for="item in categories"
                                    :key="item.id"
                                    :label="item.label"
                                    :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="百度站长">
                            <el-input v-model="website.config.baidu_zz.token" placeholder="token"></el-input>
                        </el-form-item>

                         <el-form-item label="百度熊掌">
                            <el-col :span="11">
                                <el-input v-model="website.config.baidu_xz.appid" placeholder="appid"></el-input>
                            </el-col>
                            <el-col :offset="2" :span="11">
                                <el-input v-model="website.config.baidu_xz.token" placeholder="token"></el-input>
                            </el-col>
                        </el-form-item>

                        <el-form-item label="神马平台">
                            <el-col :span="11">
                                <el-input v-model="website.config.shenma.appid" placeholder="user_name"></el-input>
                            </el-col>
                            <el-col :offset="2" :span="11">
                                <el-input v-model="website.config.shenma.token" placeholder="token"></el-input>
                            </el-col>
                        </el-form-item>

                        <el-form-item>
                            <el-button type="primary" @click="submitFrom('website')">保存</el-button>
                        </el-form-item>
                    </div>
                </el-card>
            </el-col>
        </el-form>
    </el-row>

</template>

<script>
    export default {
        props:['id'],
        data () {
            return {
                website:{
                    name:'',
                    url:'',
                    category_id:'',
                    config:{
                        baidu_zz:{
                            token:''
                        },
                        baidu_xz:{
                            appid:'',
                            token:''
                        },
                        shenma:{
                            user_name:'',
                            token:''
                        },
                    }
                },
                categories:[],
                rules: {
                    name: [
                        { required: true, message: '请输入网站名称', trigger: 'blur' },
                        { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
                    ],
                    category_id: [
                        { required: true, message: '请选择分类', trigger: 'change' }
                    ],
                    url: [
                        { required: true, message: '请输入网址', trigger: 'blur' },
                    ],
                },
                title:'',
                loading:false,
            }
        },
        methods: {
            submitFrom(formName){
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.loading = true

                        // 发送 POST 请求
                        axios({
                            method: this.id?'put':'post',
                            url: this.id?'/websites/'+this.id:'/websites',
                            data:this.website,
                        })
                        .then((response)=> {
                            this.loading = false;
                            let message = {};
                            this.$message({
                                message:response.data.msg,
                                type: 'success'
                            });
                            window.location.href="/websites";
                        })
                        .catch((error)=>{
                            console.log(error.response)
                            let message = ''
                            let status = error.response.status
                            if (status == 422) {
                                message = error.response.data.message

                            } else if (status == 403) {
                                message = '权限不足'
                            }
                            else if (status == 419) {
                                message = '非法请求'
                            }
                            else {
                                message = '系统错误:' + errors.status
                            }
                            this.loading = false
                            this.$message.error(message)
                        });
                    } else {
                        console.log('error submit!!')
                        return false
                    }
                })
            },
            getCategories(){
                axios({
                    method: 'get',
                    url: '/website-categories/api?level=1',
                })
                .then(response=> {
                    this.categories = response.data
                })
            }
        },
        created(){
            this.getCategories()
            if(this.id){
                 this.title = '编辑网站';
                //读取要编辑的文章数据
                axios.get('/websites/'+this.id)
                .then((response)=> {
                    this.website = response.data
                })
                .catch((error)=>{
                    console.log(error);

                });
            }else{
                this.title = '新建网站';
            }
        }
    }
</script>

<style lang="css">
    .input-with-select{
        width: 100px;
    }
</style>