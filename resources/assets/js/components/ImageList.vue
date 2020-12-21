<template>
    <div>
        <el-dialog :visible.sync="dialogVisible"  width="60%" top="10vh" :title="imageTitle">
          <img width="100%" :src="dialogImageUrl" alt="">
        </el-dialog>

        <el-dialog width="60%" top="10vh" title="图片列表" :visible.sync="dialogListVisible"  @close="dialogClose">
            <el-row :gutter="20">
                <el-col :span="4" v-for="(item,index) in list" :key="item.uid">
                   <el-card :body-style="{ padding: '0px' }">
                     <img height="150px" :src="item.url" class="image" @click="imagPreview(item)">
                     <!-- <div class="check"> -->
                    <el-checkbox class="image-check" v-model="checkList[index]" @change="imageChecked" :true-label="item.url"></el-checkbox>
                     <!-- </div> -->
                   </el-card>
                 </el-col>
                 <el-col :span="24" class="load-more" :class="{hide:isLoadMore}">
                      <el-button type="primary" round @click="loadMore">加载更多</el-button>
                 </el-col>
            </el-row>
            <div slot="footer" class="dialog-footer" >
               <el-button @click="dialogListVisible = false">关闭</el-button>
               <el-button type="primary" @click="checkImages">确 定</el-button>
            </div>
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
                checkList:[],
                imageTitle: '',
                dialogImageUrl: '',
                dialogVisible: false,
                isLoadMore:false
            };
        },
        methods:{
            imagPreview(file) {
               this.dialogImageUrl = file.url;
               this.imageTitle = file.name;
               this.dialogVisible = true;
           },
           loadMore(){
               axios.get('/article/images?last='+this.last)
               .then((response)=>{
                   console.log(response)
                   //判断是不是最后一页了
                   if(response.data.list){
                       response.data.list.map((value,index)=>{
                           this.list.push(value)
                       })
                       this.last = response.data.last
                   }else{
                       //最后一页了
                       this.isLoadMore = true
                   }

               })
               .catch((error)=>{
                   console.log(error);
                   this.isLoadMore = true
               });
           },
           dialogClose(){
               this.$emit('closeImageList')
           },
           checkImages(){
               this.$emit('selectImages',this.checkList)
           },
           imageChecked(res){
               console.log(res)
               console.log(this.checkList)
           }
        },
        watch:{
            dialogListVisible:function(){
                if(this.dialogListVisible){
                    //获取图片列表
                    console.log('获取图片列表')
                    axios.get('/article/images')
                    .then((response)=>{
                        console.log(response)
                        if(response.data.list.length<=18){
                            this.isLoadMore = true
                        }
                        this.list = response.data.list
                        this.last = response.data.last
                    })
                    .catch((error)=>{
                        console.log(error)
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
                }else{
                    this.list = []
                    this.checkList = []
                    this.last = ''
                    this.isLoadMore = false
                }
            }
        }
    }
</script>

<style>
    .el-card{
        position: relative;
    }
    .image-check{
        position: absolute;
        top:0;
        right: 20px;
    }
    .load-more{
        text-align: center;
    }
    .hide{
        display: none;
    }
</style>
