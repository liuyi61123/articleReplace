<template>
    <el-row>
        <el-form :model="param" :rules="rules" ref="param" label-width="80px"  v-loading="loading">
            <el-col :span="20">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="名称" prop="title">
                            <el-input v-model="param.title" placeholder="名称"></el-input>
                        </el-form-item>
                        <el-form-item label="标识符" prop="identifier">
                            <el-input v-model="param.identifier" placeholder="标识符"></el-input>
                        </el-form-item>
                        <el-form-item label="分类" prop="category">
                            <el-select v-model="param.category" placeholder="请选择">
                                <el-option
                                    v-for="item in categories"
                                    :key="item"
                                    :label="item"
                                    :value="item">
                                </el-option>
                            </el-select>
                        </el-form-item>
                         <el-form-item label="参数内容" prop="content">
                                <el-input
                                    v-model="param.content"
                                    type="textarea"
                                    :rows="20"
                                    placeholder="一行一个">
                                </el-input>
                            </el-form-item>

                        <el-form-item>
                            <el-button type="primary" @click="submitFrom('param')">保存</el-button>
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
                param:{
                    title:'',
                    category:'',
                    content:'',
                },
                categories:[
                    '通用',
                    '汽车',
                    '房屋',
                ],
                rules: {
                    title: [
                        { required: true, message: '请输入名称', trigger: 'blur' },
                        { min: 1, max: 30, message: '长度在 1 到 30 个字符', trigger: 'blur' }
                    ],
                    identifier: [
                        { required: true, message: '请输入标识符', trigger: 'blur' },
                        { min: 1, max: 30, message: '长度在 1 到 30 个字符', trigger: 'blur' }
                    ],
                    category: [
                        { required: true, message: '请输入分类名称', trigger: 'blur' },
                    ],
                    content: [
                        { required: true, message: '请输入内容', trigger: 'blur' },
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
                        let param = this.param
                        axios({
                            method: this.id?'put':'post',
                            url: this.id?'/article/paragraphs/'+this.id:'/article/paragraphs',
                            data:param,
                        })
                        .then((response)=> {
                            this.loading = false;
                            let message = {};
                            this.$message({
                                message: response.data.msg,
                                type: 'success'
                            });
                            window.location.href="/article/paragraphs";
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
            }
        },
        created(){
            if(this.id){
                 this.title = '编辑段落';
                //读取要编辑的文章数据
                axios.get('/article/paragraphs/'+this.id)
                .then((response)=> {
                    this.param = response.data
                })
                .catch((error)=>{
                    console.log(error);

                });
            }else{
                this.title = '新建段落';
            }
        }
    }
</script>

<style lang="css">
    .input-with-select{
        width: 100px;
    }
</style>