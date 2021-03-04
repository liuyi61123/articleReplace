<template>
    <el-row :gutter="20">
        <el-form label-width="100px" :model="template" :rules="rules" ref="template" v-loading="loading">
            <el-col :span="24">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="名称" prop="name">
                            <el-input v-model="template.name" placeholder="模板名称"></el-input>
                        </el-form-item>
                        <el-form-item label="固定参数">
                            <el-checkbox-group v-model="template.fixed_params">
                                <el-checkbox-button v-for="fixed_param of fixed_params" :label="fixed_param.id" :key="fixed_param.id">{{fixed_param.title}}-{{fixed_param.id}}</el-checkbox-button>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="自定义参数">
                              <el-transfer 
                                filterable
                                :filter-method="customParamsfilter"
                                filter-placeholder="请输入参数名称"
                                :titles="['所有参数', '已选参数']"
                                v-model="template.custom_params" 
                                :data="custom_params">
                              </el-transfer>
                            
                        </el-form-item>
                        <el-form-item label="自定义段落">
                              <el-transfer 
                                filterable
                                :filter-method="customParamsfilter"
                                filter-placeholder="请输入参数名称"
                                :titles="['所有参数', '已选参数']"
                                v-model="template.custom_paragraphs" 
                                :data="custom_paragraphs">
                              </el-transfer>
                        </el-form-item>
                        <el-form-item label="内容" prop="content">
                            <el-input type="textarea" :autosize="{ minRows: 20}" v-model="template.content" placeholder="模板内容"></el-input>
                        </el-form-item>
                        <el-form-item label="图片目录" prop="images">
                            <el-input v-model="template.images" placeholder="图片目录">
                                <template slot="prepend">uploads/templates/</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item label="固定段落目录">
                            <el-input v-model="template.fixed_paragraphs" placeholder="固定段落目录前缀">
                                <template slot="prepend">uploads/paragraphs/</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="submitFrom('template')">保存</el-button>
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
                template:{
                    name:'',
                    content:'',
                    fixed_params:[],
                    fixed_paragraphs:'',
                    custom_params:[],
                    custom_paragraphs:[],
                    images:''
                },
                fixed_params:[
                    {
                        id:'city',
                        title:'城市'
                    },
                    {
                        id:'car',
                        title:'汽车'
                    }
                ],
                custom_params:[],
                custom_paragraphs:[],
                title:'',
                loading:false,
                rules: {
                    name: [
                        { required: true, message: '请输入名称', trigger: 'blur' },
                        { min: 1, max: 30, message: '长度在 1 到 30 个字符', trigger: 'blur' }
                    ],
                    content: [
                        { required: true, message: '请输入内容', trigger: 'blur' },
                    ],
                    images: [
                        { required: true, message: '请输入图片目录', trigger: 'blur' },
                    ]
                }
            }
        },
        methods: {
            customParamsfilter(query, item){
                return item.title.indexOf(query) > -1;
            },
            submitFrom(formName){
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.loading = true
                        // 发送 POST 请求
                        axios({
                            method: this.id?'put':'post',
                            url: this.id?'/article/templates/'+this.id:'/article/templates',
                            data:this.template,
                        })
                        .then((response)=> {
                            this.loading = false;
                            let message = {};
                            this.$message({
                                message: '修改成功',
                                type: 'success'
                            });
                            window.location.href="/article/templates";
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
                    }
                });
            },
            getCustomParams(){
                axios.post('/article/api/params')
                .then((response)=> {
                    console.log(response)
                    this.custom_params = response.data.map((item)=>{
                        return {
                            key:item.id,
                            label:item.title+'-'+item.identifier,
                            disabled:false,
                            title:item.title
                        }
                    })
                })
                .catch((error)=>{
                    console.log(error);

                });
           },
           getCustomParagraphs(){
                axios.post('/article/api/paragraphs')
                .then((response)=> {
                    console.log(response)
                    this.custom_paragraphs = response.data.map((item)=>{
                        return {
                            key:item.id,
                            label:item.title+'-'+item.identifier,
                            disabled:false,
                            title:item.title
                        }
                    })
                })
                .catch((error)=>{
                    console.log(error);

                });
           }
        },
        created(){
            this.getCustomParams();
            this.getCustomParagraphs();
            if(this.id){
                 this.title = '编辑模板';
                //读取要编辑的文章数据
                axios.get('/article/templates/'+this.id+'/edit')
                .then((response)=> {
                    this.template = response.data
                    this.template.images = response.data.images ||[]
                    this.template.custom_paragraphs = response.data.custom_paragraphs ||[{name:'',content:''}]
                })
                .catch((error)=>{
                    console.log(error);

                });
            }else{
                this.title = '新建模板';
            }
        }
    }
</script>
