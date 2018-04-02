<template>
    <el-row :gutter="20">
        <el-form label-position="top" label-width="80px"  v-loading="loading">
            <el-col :span="24">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="名称">
                            <el-input v-model="template.name" placeholder="模板名称"></el-input>
                        </el-form-item>
                        <el-form-item label="内容">
                            <el-input type="textarea" :autosize="{ minRows: 20}" v-model="template.content" placeholder="模板内容"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="submitFrom()">保存</el-button>
                        </el-form-item>
                    </div>
                </el-card>
            </el-col>
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
                },
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
                    url: this.id?'/templates/'+this.id:'/templates',
                    data:this.template
                })
                .then((response)=> {
                        this.loading = false;
                        let message = {};
                        if(response.data.status == 200){
                            this.$message({
                                message: '修改成功',
                                type: 'success'
                            });
                            window.location.href="/templates";
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
            }
        },
        created(){
            if(this.id){
                 this.title = '编辑模板';
                //读取要编辑的文章数据
                axios.get('/templates/'+this.id+'/edit')
                .then((response)=> {
                    console.log(response);
                    this.template = response.data;
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
