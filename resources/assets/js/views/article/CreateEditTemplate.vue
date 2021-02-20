<template>
    <el-row :gutter="20">
        <el-form label-width="100px" :model="template" :rules="rules" ref="template" v-loading="loading">
            <el-col :span="15">
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
                            <!-- <el-checkbox-group v-model="template.custom_params" :min="0" :max="4">
                                <el-checkbox-button v-for="custom_param of custom_params" :label="custom_param.id" :key="custom_param.id">{{custom_param.title}}-{{custom_param.identifier}}</el-checkbox-button>
                            </el-checkbox-group> -->
                        </el-form-item>
                        <el-form-item label="内容" prop="content">
                            <el-input type="textarea" :autosize="{ minRows: 20}" v-model="template.content" placeholder="模板内容"></el-input>
                        </el-form-item>
                        <!-- <el-form-item label="图片">
                            <el-button type="primary" @click="imageListAction">从图片库选择</el-button>
                            <ImageList :dialogListVisible="imageList" @closeImageList="closeImageList" @selectImages="selectImages"></ImageList>
                            <el-upload
                              action="/article/templates/upload_image"
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
                        </el-form-item> -->
                        <!-- <el-dialog :visible.sync="dialogVisible">
                          <img width="100%" :src="dialogImageUrl" alt="">
                        </el-dialog> -->
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

            <el-col :span="9">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>段落</span>
                        <el-button type="success" size="small" style="float: right;" @click="addParagraph()">添加段落</el-button>
                    </div>

                    <div class="text item">
                        <div v-for="(paragraph,index) in template.custom_paragraphs" :key="index">
                            <el-form-item label="段落标示">
                                <el-input v-model="paragraph.name" placeholder="段落名称（便于区分）"></el-input>
                            </el-form-item>
                            <el-form-item label="段落内容">
                                <!-- <el-col :span="24"> -->
                                    <el-input
                                      v-model="paragraph.content"
                                      type="textarea"
                                      :rows="10"
                                      placeholder="一行一个">
                                    </el-input>
                                 <!-- </el-col> -->
                            </el-form-item>
                            <el-form-item>
                                <el-button style="float: right;" size="small" type="danger" icon="el-icon-delete" @click="deleteParagraph(index)"></el-button>
                            </el-form-item>
                        </div>
                    </div>
                </el-card>
            </el-col>
        </el-form>
    </el-row>

</template>

<script>
    // import ImageList from '../../components/ImageList'
    export default {
        // components:{
        //     ImageList
        // },
        props:['id'],
        data () {
            return {
                template:{
                    name:'',
                    content:'',
                    fixed_params:[],
                    fixed_paragraphs:'',
                    custom_params:[],
                    custom_paragraphs:[
                        {
                            name:'',
                            content:''
                        }
                    ],
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
                custom_params:[
                ],
                paragraphIndex:0,
                title:'',
                loading:false,
                // dialogImageUrl: '',
                // dialogVisible: false,
                // imageList:false
                rules: {
                    name: [
                        { required: true, message: '请输入名称', trigger: 'blur' },
                        { min: 1, max: 30, message: '长度在 1 到 30 个字符', trigger: 'blur' }
                    ],
                    content: [
                        { required: true, message: '请输入内容', trigger: 'blur' },
                    ],
                    images: [
                        { required: true, message: '请输入分类名称', trigger: 'blur' },
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
            fixedParamsChange(val){
                console.log(val)
                console.log(this.template.fixed_params)
            },
            customParamsChange(val){
                console.log(val)
                console.log(this.template.custom_params)
            },
            removeImage(file, fileList) {
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
           },
           addParagraph(){
               //添加参数
               if(this.paragraphIndex >= 100){
                   this.$message.error('不能超过100个参数')
               }else{
                   this.paragraphIndex++
                   Vue.set(this.template.custom_paragraphs, this.template.custom_paragraphs.length, {name:'',content:''})
               }
           },
           deleteParagraph(index){
               //删除参数
               this.template.custom_paragraphs.splice(index, 1)
               this.paragraphIndex--
               console.log(this.paragraphIndex)

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
           }
        },
        created(){
            this.getCustomParams();
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
