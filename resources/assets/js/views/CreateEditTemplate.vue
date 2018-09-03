<template>
    <el-row :gutter="20">
        <el-form label-position="top" label-width="80px"  v-loading="loading">
            <el-col :span="16" :offset="4">
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
                        <el-form-item label="图片">
                            <el-upload
                              action="/templates/upload_image"
                              accept="image/*"
                              list-type="picture-card"
                              name="image"
                              :file-list="template.images"
                              :on-preview="imagPreview"
                              :on-success="imageUploadSuccess"
                              :on-remove="removeImage">
                              <i class="el-icon-plus"></i>
                            </el-upload>
                        </el-form-item>
                        <el-dialog :visible.sync="dialogVisible">
                          <img width="100%" :src="dialogImageUrl" alt="">
                        </el-dialog>
                        <el-form-item label="段落">
                            <el-input type="textarea" :autosize="{ minRows: 20}" v-model="template.paragraphs" placeholder="段落"></el-input>
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
                    paragraphs:'',
                    images:[]
                },
                title:'',
                loading:false,
                dialogImageUrl: '',
                dialogVisible: false
            }
        },
        methods: {
            submitFrom(){
                this.loading = true;
                // 发送 POST 请求
                axios({
                    method: this.id?'put':'post',
                    url: this.id?'/templates/'+this.id:'/templates',
                    data:this.template,
                })
                .then((response)=> {
                        this.loading = false;
                        let message = {};
                        this.$message({
                            message: '修改成功',
                            type: 'success'
                        });
                        window.location.href="/templates";
                   })
                   .catch((error)=>{
                       console.log(error);
                       this.loading = false;
                       this.$message({
                           message: '修改失败',
                           type: 'error'
                       });
                   });
            },
            removeImage(file, fileList) {
                console.log(file)
                console.log(fileList)

                axios({
                    method:'delete',
                    url:'/templates/delete_image',
                    data:{
                        url:file.url
                    }
                })
                .then((response)=> {
                    console.log(response)
                    this.template.images.map((value,index)=>{
                        if(value.uid == file.uid){
                            this.template.images.splice(index,1);
                        }
                    })
                })
                .catch((error)=>{
                    console.log(error);
                });

                console.log(this.template.images)
            },
            imageUploadSuccess(response, file, fileList) {
                console.log(response)
                console.log(file)
                console.log(fileList)
                this.template.images.push({
                    uid:file.uid,
                    url:file.response.data
                })
                // this.template.images = []
                // fileList.map((value,index)=>{
                //     let url = value.response?value.response.data:value.url
                //     this.template.images.push({
                //         'url':url
                //     });
                // })
                console.log(this.template.images)
            },
            imagPreview(file) {
               this.dialogImageUrl = file.url;
               this.dialogVisible = true;
           }
        },
        created(){
            if(this.id){
                 this.title = '编辑模板';
                //读取要编辑的文章数据
                axios.get('/templates/'+this.id+'/edit')
                .then((response)=> {
                    console.log(response);
                    this.template = response.data
                    this.template.images = response.data.images ||[]
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
