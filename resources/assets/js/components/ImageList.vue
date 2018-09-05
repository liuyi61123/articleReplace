<template>
    <div class="">
        <el-dialog :visible.sync="dialogVisible"  width="60%" top="10vh">
          <img width="100%" :src="dialogImageUrl" alt="">
        </el-dialog>

        <el-dialog width="60%" top="10vh" title="图片列表" :visible.sync="dialogListVisible"  @close="dialogClose">
            <el-upload
              action="/images/store"
              accept="image/*"
              list-type="picture-card"
              name="image"
              :file-list="list"
              :on-preview="imagPreview"
              :on-success="imageUploadSuccess"
              :on-remove="removeImage">
              <i class="el-icon-plus"></i>
            </el-upload>
        </el-dialog>
    </div>

</template>

<script>
    export default {
        props:['dialogListVisible'],
        data () {
            return {
                list:[],
                last:'',
                dialogImageUrl: '',
                dialogVisible: false,
            };
        },
        methods:{
            dialogClose(){
                console.log('关闭')
            },
            removeImage(file, fileList) {
                console.log(file)
                console.log(fileList)

                // axios({
                //     method:'delete',
                //     url:'/images/destory',
                //     data:{
                //         url:file.url
                //     }
                // })
                // .then((response)=> {
                //     console.log(response)
                //     this.template.images.map((value,index)=>{
                //         if(value.uid == file.uid){
                //             this.template.images.splice(index,1);
                //         }
                //     })
                // })
                // .catch((error)=>{
                //     console.log(error);
                // });
                //
                // console.log(this.template.images)
            },
            imageUploadSuccess(response, file, fileList) {
                console.log(response)
                console.log(file)
                console.log(fileList)
                // this.template.images.push({
                //     uid:file.uid,
                //     url:file.response.data
                // })
                // this.template.images = []
                // fileList.map((value,index)=>{
                //     let url = value.response?value.response.data:value.url
                //     this.template.images.push({
                //         'url':url
                //     });
                // })
            },
            imagPreview(file) {
               this.dialogImageUrl = file.url;
               this.dialogVisible = true;
           },
           imageListAction(){
               this.imageList = this.imageList?false:true
               console.log(this.imageList)
           }
        },
        watch:{
            dialogListVisible:function(){
                if(this.dialogListVisible){
                    //获取图片列表
                    console.log('获取图片列表')
                    axios.get('/images?last='+this.last)
                    .then((response)=>{
                        console.log(response)
                        this.list = response.data.list
                        this.last = response.data.last
                    })
                    .catch((error)=>{
                        console.log(error);
                    });
                }else{
                    this.list = []
                    this.last = ''
                }
            }
        }
    }
</script>
