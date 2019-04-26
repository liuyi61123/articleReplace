<template>
    <el-row :gutter="20">
        <el-col :span="24">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>百度Url</span>
                </div>
                    <div class="text item">
                    <el-form label-width="100px">

                      <el-form-item label="文章目录">
                          <el-input
                            type="textarea"
                            :autosize="{ minRows: 4,maxRows:50}"
                            :rows="2"
                            placeholder="请输入url，一行一个"
                            v-model="urls">
                          </el-input>
                      </el-form-item>

                      <el-form-item>
                        <el-button type="primary" @click="submitForm()">执行</el-button>
                      </el-form-item>
                    </el-form>
                </div>
            </el-card>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        data () {
            return {
                loading:false,
                urls:'',
            }
        },
        methods: {
            submitForm(){
                this.fullScreen(true)
                let urls = this.urls
                if(urls){
                    axios.post('/baidu_urls',{
                        urls:urls
                    })
                    .then(res=> {
                        this.fullScreen(false)
                        console.log(res)
                        if(res.data.status == 200){
                            this.$message({
                                message: '已完成',
                                type: 'success'
                            })
                            //  清空urls
                            this.urls = ''
                            window.location.href = '/baidu_urls'
                        }else{
                            this.$message.error(res.data.msg)
                        }
                    })
                    .catch(error=>{
                       console.log(error)
                       this.fullScreen(false)
                       this.$message.error('错了哦，这是一条错误消息')
                   })
               } else{
                    this.$message.error('url不能为空')
               }

            },
            fullScreen(bool) {
                if(bool){
                    this.loading = this.$loading({
                      lock: true,
                      text: '正在生成中',
                      spinner: 'el-icon-loading',
                      background: 'rgba(0, 0, 0, 0.7)'
                    })
                }else{
                    this.loading.close()
                }
            }
        },
        created(){

        }
    }
</script>
