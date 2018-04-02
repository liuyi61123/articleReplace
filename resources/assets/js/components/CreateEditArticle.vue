<template>
    <el-row :gutter="20">
        <el-form label-position="top" label-width="80px"  v-loading="loading">
            <el-col :span="16">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="模板">
                            <el-select style="width:100%" v-model="article.template_id" placeholder="请选择">
                                <el-option  v-for="template in templates"
                                  :key="template.id"
                                  :label="template.name"
                                  :value="template.id">
                              </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="标题">
                            <el-input v-model="article.title" placeholder="文章标题"></el-input>
                        </el-form-item>
                        <el-form-item label="关键字">
                            <el-input v-model="article.keywords" placeholder="请输入关键字"></el-input>
                        </el-form-item>
                        <el-form-item label="描述">
                            <el-input type="textarea" :autosize="{ minRows: 4, maxRows: 8}" v-model="article.description" placeholder="请输入描述"></el-input>
                        </el-form-item>
                        <el-form-item label="内容">
                            <el-input type="textarea" :autosize="{ minRows: 8, maxRows: 16}" v-model="article.content" placeholder="内容"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="submitFrom()">保存</el-button>
                        </el-form-item>
                    </div>
                </el-card>
            </el-col>
            <el-col :span="8">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>参数设置</span>
                        <el-button type="success" size="small" style="float: right;" @click="addParam()">添加参数</el-button>
                    </div>

                    <div class="text item">
                        <div v-for="(param,index) in article.params">
                            <el-form-item>
                                <el-input v-model="param.name" placeholder="参数名称{$param}"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-input type="textarea" :autosize="{ minRows: 4, maxRows: 8}" v-model="param.content"   placeholder="需要替换的内容,一行一个"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button style="float: right;" size="small" type="danger" icon="el-icon-delete" @click="deleteParam(index)"></el-button>
                            </el-form-item>
                        </div>
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
                templates:[],
                article:{
                    template_id:'',
                    titile:'',
                    keywords:'',
                    description:'',
                    content:'',
                    params:
                    [
                        {
                            name:'',
                            content:''
                        }
                    ],
                },
                paramsIndex:0,
                title:'',
                loading:false
            }
        },
        methods: {
            submitFrom(){
                this.loading = true;
                // 发送 POST 请求
                axios({
                    method: this.id?'put':'post',
                    url: this.id?'/articles/'+this.id:'/articles',
                    data:this.article
                })
                .then((response)=> {
                        this.loading = false;
                        let message = {};
                        if(response.data.status == 200){
                            this.$message({
                                message: '修改成功',
                                type: 'success'
                            });
                            window.location.href="/articles";
                        }else{
                            this.$message({
                                message: '修改失败',
                                type: 'error'
                            });
                        }
                   })
                   .catch((error)=>{
                       console.log(error);
                       this.loading = false;
                       this.$message.error('错了哦，这是一条错误消息');
                   });
            },
            addParam(){
                //添加参数
                this.paramsIndex++;
                Vue.set(this.article.params, this.article.params.length, {name:'',content:''});
            },
            deleteParam(index){
                //删除参数
                this.article.params.splice(index, 1);
            }
        },
        created(){
            if(this.id){
                this.title = '编辑文章';
                //读取要编辑的文章数据
                axios.get('/articles/'+this.id+'/edit')
                .then((response)=> {
                    console.log(response);
                    this.article = response.data;
                })
                .catch((error)=>{
                    console.log(error);
                });
            }else{
                this.title = '新建文章';
            }
            //获取模板列表
            axios.get('/templates')
            .then((response)=> {
                console.log(response);
                this.templates = response.data;
            })
            .catch((error)=>{
                console.log(error);
            });
        }
    }
</script>
