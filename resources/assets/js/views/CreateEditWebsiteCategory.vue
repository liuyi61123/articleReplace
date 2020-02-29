<template>
    <el-row>
        <el-form :model="category" :rules="rules" ref="category" label-width="80px"  v-loading="loading">
            <el-col :span="20">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="名称" prop="label">
                            <el-input v-model="category.label" placeholder="名称"></el-input>
                        </el-form-item>
                        <el-form-item label="排序" prop="sort">
                            <el-input-number v-model="category.sort" :min="1" :max="100000" label="排序"></el-input-number>
                        </el-form-item>
                        <el-form-item label="父级" prop="parent_id">
                            <el-select v-model="category.parent_id" placeholder="请选择">
                                <el-option
                                    v-for="item in categories"
                                    :key="item.id"
                                    :label="item.label"
                                    :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item>
                            <el-button type="primary" @click="submitFrom('category')">保存</el-button>
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
                category:{
                    label:'',
                    sort:'',
                    parent_id:'',
                },
                categories:[],
                rules: {
                    label: [
                        { required: true, message: '请输入分类名称', trigger: 'blur' },
                        { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
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
                        let category = this.category
                        if(!this.category.parent_id){
                            category.parent_id = 0
                        }
                        axios({
                            method: this.id?'put':'post',
                            url: this.id?'/website-categories/'+this.id:'/website-categories',
                            data:category,
                        })
                        .then((response)=> {
                            this.loading = false;
                            let message = {};
                            this.$message({
                                message: response.data.msg,
                                type: 'success'
                            });
                            window.location.href="/website-categories";
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
                    url: '/website-categories/api',
                })
                .then(response=> {
                    this.categories = response.data
                })
            }
        },
        created(){
            this.getCategories()
            if(this.id){
                 this.title = '编辑网站分类';
                //读取要编辑的文章数据
                axios.get('/website-categories/'+this.id)
                .then((response)=> {
                    this.category = response.data
                })
                .catch((error)=>{
                    console.log(error);

                });
            }else{
                this.title = '新建网站分类';
            }
        }
    }
</script>

<style lang="css">
    .input-with-select{
        width: 100px;
    }
</style>