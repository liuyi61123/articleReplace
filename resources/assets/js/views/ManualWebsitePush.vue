<template>
    <el-row :gutter="20">
        <el-form :model="push" :rules="rules" ref="push" label-width="80px" v-loading="loading">
            <el-col :span="16">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>手动提交</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="网站" prop="website_id">
                            <el-select class="select" v-model="push.website_id" placeholder="请选择">
                                <el-option
                                    v-for="item in websites"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="名称" prop="platforms">
                            <el-checkbox-group :min="1" v-model="push.platforms">
                                <el-checkbox v-for="platform of platforms" :label="platform.id" :key="platform.id" @change="checkedPlatform">{{platform.name}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="网址" prop="urls">
                            <el-input
                                type="textarea"
                                :rows="10"
                                placeholder="请输入网址"
                                v-model="push.urls">
                            </el-input>
                        </el-form-item>

                        <el-form-item label="条数" prop="number">
                            <el-input-number v-model="push.number" :min="1" :max="1000" label="条数"></el-input-number>
                        </el-form-item>

                        <el-form-item>
                            <el-button type="primary" :disabled="disabled" @click="submitFrom('push')">提交</el-button>
                        </el-form-item>
                    </div>
                </el-card>
            </el-col>
            <el-col v-show="result.length>0" :span="8">
                <el-card class="box-card result">
                    <div slot="header" class="clearfix">
                        <span>返回结果</span>
                    </div>
                    <div>
                        <pre v-for="item in result" :key="item.id">{{item}}</pre>
                    </div>
                </el-card>
            </el-col>
        </el-form>
    </el-row>

</template>

<script>
    export default {
        data () {
            return {
                disabled:false,
                push:{
                    type:'manual',
                    website_id:'',
                    platforms:[],
                    urls:'',
                    number:1
                },
                platforms:[
                    {
                        id:'baidu_zz',
                        name:'百度站长'
                    },
                    {
                        id:'shenma',
                        name:'神马'
                    },
                    {
                        id:'baidu_xz_t',
                        name:'百度熊掌(天级)'
                    },
                    {
                        id:'baidu_xz_z',
                        name:'百度熊掌(周级)'
                    },
                ],
                websites:[],
                result:[],
                rules: {
                    website_id: [
                        { required: true, message: '请选择网站', trigger: 'change' }
                    ],
                    platforms: [
                        { type: 'array', required: true, message: '请至少选择一个平台', trigger: 'change' }
                    ],
                    urls: [
                        { required: true, message: '请输入网址', trigger: 'blur' },
                    ],
                    number: [
                        { required: true, message: '请输入数值', trigger: 'blur' },
                    ],
                },
                loading:false,
            }
        },
        methods: {
            checkedPlatform(e){
                console.log(this.push.platforms)
            },
            submitFrom(formName){
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        //按钮禁止点击
                        this.disabled = true
                        this.sendPost()
                    } else {
                        console.log('error submit!!')
                        return false
                    }
                })
            },
            sendPost(){
                //先清空结果
                this.result = []

                let push = this.push
                let urls = push.urls.split(/[\s\n]/)
                //组合url
                urls = array_number_data(urls,this.push.number)
                
                //计算最大请求数
                let max = (push.platforms.length) * (urls.length)
                let i = 0
                push.platforms.map(item=>{
                    urls.map(async url=>{
                        let postData = {
                            type:'manual',
                            website_id:push.website_id,
                            platform:item,
                            urls:url,
                        }

                        i++
                        await this.sendUrl(postData,max,i)
                    })
                })
            },
            sendUrl(data,max,now){
                console.log(max)
                console.log(now)
                axios({
                    method: 'post',
                    url: '/website-pushes',
                    data:data,
                })
                .then((response)=> {
                    this.result.push(response.data.data)
                })
                .catch((error)=>{
                    console.log(error.response)
                    let status = error.response.status
                    let message = {
                            status:status,
                            data:error.response.data
                        }
                    
                    this.result.push(message)
                })
                if(max == now){
                    this.disabled = false
                }
            },
            getWebsites(){
                axios({
                    method: 'get',
                    url: '/websites/api',
                })
                .then(response=> {
                    this.websites = response.data
                })
            }
        },
        created(){
            this.getWebsites()
        }
    }
</script>

<style lang="css">
    .input-with-select{
        width: 100px;
    }
    .result{
        background-color: #ccc;
        color:#fff;
    }
    .select{
        display: block;
    }
</style>