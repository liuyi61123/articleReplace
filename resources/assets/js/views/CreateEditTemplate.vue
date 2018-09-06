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
                            <el-button type="primary" @click="imageListAction">从图片库选择</el-button>
                            <ImageList :dialogListVisible="imageList" @closeImageList="closeImageList" @selectImages="selectImages"></ImageList>
                            <el-upload
                              action="/templates/upload_image"
                              accept="image/*"
                              list-type="picture-card"
                              name="image"
                              :multiple="true"
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
    import ImageList from '../components/ImageList'
    export default {
        components:{
            ImageList
        },
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
                dialogVisible: false,
                imageList:false
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
            },
            removeImage(file, fileList) {
                // axios({
                //     method:'delete',
                //     url:'/templates/delete_image',
                //     data:{
                //         url:file.url
                //     }
                // })
                // .then((response)=> {
                //     this.template.images.map((value,index)=>{
                //         if(value.uid == file.uid){
                //             this.template.images.splice(index,1);
                //         }
                //     })
                // })
                // .catch((error)=>{
                //     console.log(error);
                // });
                this.template.images.map((value,index)=>{
                    if(value.uid == file.uid){
                        this.template.images.splice(index,1);
                    }
                })
            },
            imageUploadSuccess(response, file, fileList) {
                this.template.images = fileList
                this.template.images.map((value,index)=>{
                    this.template.images[index].url = value.response?value.response.data:value.url
                    delete this.template.images[index].response
                    delete this.template.images[index].raw
                })
            },
            imagPreview(file) {
               this.dialogImageUrl = file.url;
               this.dialogVisible = true;
           },
           imageListAction(){
               this.imageList = this.imageList?false:true
               console.log(this.imageList)
           },
           closeImageList(){
               this.imageList = false
           },
           selectImages(images){
               console.log(images)
               images.map((value,index)=>{
                   this.template.images.push({
                       url:value
                   })
               })
               this.closeImageList()
               console.log(this.template.images)
           }
        },
        created(){
            if(this.id){
                 this.title = '编辑模板';
                //读取要编辑的文章数据
                axios.get('/templates/'+this.id+'/edit')
                .then((response)=> {
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
