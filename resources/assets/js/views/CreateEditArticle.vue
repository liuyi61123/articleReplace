<template>
    <el-row :gutter="20">
        <el-form label-width="80px">
            <el-col :span="16">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="模板">
                            <el-col :span="4">
                                <el-select style="width:100%" v-model="article.template_id" placeholder="请选择">
                                    <el-option  v-for="template in templates"
                                      :key="template.id"
                                      :label="template.name"
                                      :value="template.id">
                                  </el-option>
                                </el-select>
                            </el-col>
                        </el-form-item>
                        <el-form-item label="市">
                            <el-col :span="4">
                                <el-select v-model="article.city.data" placeholder="请选择" @change="changeCity">
                                    <el-option  v-for="city in citys"
                                      :key="city.id"
                                      :label="city.name"
                                      :value="city.id">
                                    </el-option>
                                  </el-select>
                            </el-col>
                            <el-col :span="3">
                              <el-input-number v-model="article.city.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            </el-col>
                        </el-form-item>
                        <el-form-item label="区">
                            <el-col :span="24">
                                <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                <el-checkbox-group :min="1" v-model="article.countys.data">
                                      <el-checkbox v-for="county of countys" :label="county.name" :key="county.id" @change="handleCheckedCitiesChange"></el-checkbox>
                                </el-checkbox-group>
                            </el-col>
                            <el-col :span="3">
                              <el-input-number v-model="article.countys.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            </el-col>
                        </el-form-item>
                        <!-- <el-form-item label="类型">
                            <el-col :span="4">
                                <el-select v-model="article.type" placeholder="请选择">
                                    <el-option  v-for="type in types"
                                      :key="type.id"
                                      :label="type.name"
                                      :value="type.id">
                                  </el-option>
                                </el-select>
                            </el-col>
                        </el-form-item> -->
                        <el-form-item label="品牌">
                          <div v-for="(param,index) in article.cars.data">
                            <el-col :span="4">
                              <el-select v-model="param.brand" filterable placeholder="请选择" @change="changeCar(param.brand,index)">
                                <el-option
                                  v-for="car in cars"
                                  :key="car.brand.id"
                                  :label="car.brand.name"
                                  :value="car.brand.id">
                                </el-option>
                              </el-select>
                            </el-col>

                            <el-col :span="18">
                                <template v-if="param.brand > 0">
                                    <div class="line" v-for="(model,key) in cars[param.brand].models">
                                        <el-checkbox v-model="param.models[key].name" :true-label="model.name">{{model.name}}</el-checkbox>
                                        <el-input v-model="param.models[key].min" style="width:20%" min="1" placeholder="最小值" type="number">
                                            <template slot="append">万</template>
                                        </el-input>
                                        <el-input v-model="param.models[key].max" style="width:20%" min="1" placeholder="最大值" type="number">
                                            <template slot="append">万</template>
                                        </el-input>
                                    </div>
                                </template>
                            </el-col>

                            <el-col :span="2">
                                <el-button v-if="index == 0" type="primary" icon="el-icon-plus" @click="addCar()"></el-button>
                                <el-button v-if="index > 0" type="danger" icon="el-icon-minus" @click="deleteCar(index)"></el-button>
                            </el-col>
                            </div>
                            <el-col :span="24">
                                <el-input-number v-model="article.cars.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            </el-col>
                        </el-form-item>

                        <el-form-item label="价格排序">
                            <el-input-number v-model="article.cars.price_sort" controls-position="right" :min="1" :max="10"></el-input-number>
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
                        <span>额外参数</span>
                        <el-button type="success" size="small" style="float: right;" @click="addParam()">添加参数</el-button>
                    </div>

                    <div class="text item">
                        <div v-for="(param,index) in article.params">
                            <el-form-item label="参数名称">
                                <el-col :span="12">
                                    <el-input v-model="param.name" placeholder="{$param}"></el-input>
                                </el-col>
                                <el-col :span="4">
                                    <el-input-number v-model="param.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                                </el-col>
                            </el-form-item>
                            <el-form-item label="参数内容">
                                <el-col :span="24">
                                    <el-input
                                      v-model="param.content"
                                      type="textarea"
                                      :rows="5"
                                      placeholder="一行一个">
                                    </el-input>
                                 </el-col>
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
                checkAll: false,
                isIndeterminate: true,
                templates:[],
                types:[
                    {
                        id:1,
                        name:'车贷'
                    },
                    {
                        id:2,
                        name:'房贷'
                    }
                ],
                citys:[],
                countys:[],
                cars:[],
                article:{
                    template_id:1,
                    countys:{
                        sort:2,
                        data:[]
                    },
                    // type:1,
                    city:{
                        sort:1,
                        data:1
                    },
                    cars:{
                        sort:3,
                        price_sort:4,
                        data:[
                          {
                            brand:'',
                            models:[
                            ]
                          }
                        ]
                    },
                    params:
                    [
                        {
                            sort:5,
                            name:'',
                            content:''
                        }
                    ],
                },
                paramsIndex:0,
                title:'',
                loading:''
            }
        },
        methods: {
            changeCity(e){
                this.getCountys(e)
            },
            handleCheckAllChange(val) {
                let countys = [];
                this.countys.map((value,key)=>{
                    countys[key] = value.name
                })
                this.article.countys.data = val ? countys : [];
                this.isIndeterminate = false;
            },
            handleCheckedCitiesChange(value) {
                let checkedCount = value.length;
                this.checkAll = checkedCount === this.countys.length;
                this.isIndeterminate = checkedCount > 0 && checkedCount < this.countys.length;
            },
            changeCar(e,index){
                this.article.cars.data[index].models = []

                this.cars[e].models.map((value,key)=>{
                    let model = {
                        name:'',
                        min:1,
                        max:10
                    }
                    this.article.cars.data[index].models.push(model)
                })
            },
            addCar(){
                //添加汽车信息参数
                this.article.cars.data.push({
                    brand:'',
                    models:[]
                });
            },
            deleteCar(index){
                //删除汽车参数
                this.article.cars.data.splice(index, 1)
            },
            submitFrom(){
                console.log(this.article);

                this.fullScreen(true)
                // 发送 POST 请求
                axios({
                    method: this.id?'put':'post',
                    url: this.id?'/articles/'+this.id:'/articles',
                    data:this.article
                })
                .then((response)=> {
                        this.fullScreen(false)
                        let message = {};
                        if(response.data.status == 200){
                            this.$message({
                                message: '保存成功',
                                type: 'success'
                            });
                            window.location.href="/articles";
                        }else{
                            this.$message({
                                message: '保存失败',
                                type: 'error'
                            });
                        }
                   })
                   .catch((error)=>{
                       console.log(error)
                       this.fullScreen(false)

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
            addParam(){
                //添加参数
                if(this.paramsIndex >= 2){
                    this.$message.error('不能超过3个参数')
                }else{
                    this.paramsIndex++
                    Vue.set(this.article.params, this.article.params.length, {sort:this.paramsIndex+5,name:'',content:[]})
                }
            },
            deleteParam(index){
                //删除参数
                this.article.params.splice(index, 1)
                this.paramsIndex--
                console.log(this.paramsIndex)

            },
            //获取模板列表
            getTemplates(){
                axios.get('/templates')
                .then((response)=> {
                    this.templates = response.data
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取市区信息
            getCitys(id){
                axios.get('/articles/citys/'+id)
                .then((response)=> {
                    this.citys = response.data
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取市区信息
            getCountys(id){
                axios.get('/articles/citys/'+id)
                .then((response)=> {
                    this.countys = response.data
                    if(!this.id){
                        response.data.map((value,index)=>{
                            this.article.countys.data.push(value.name)
                        })
                    }
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取品牌信息
            getCars(){
                axios.get('/articles/cars')
                .then((response)=> {
                    console.log(response);
                    this.cars = response.data
                })
                .catch((error)=>{
                    console.log(error)
                });
            },
            fullScreen(bool) {
                if(bool){
                    this.loading = this.$loading({
                      lock: true,
                      text: 'Loading',
                      spinner: 'el-icon-loading',
                      background: 'rgba(0, 0, 0, 0.7)'
                    });
                }else{
                    this.loading.close();
                }
            }
        },
        created(){
            //开启loading
            this.fullScreen(true)

            //编辑的情况
            if(this.id){
                this.title = '编辑文章';
                //读取要编辑的文章数据
                axios.get('/articles/'+this.id+'/edit')
                .then((response)=> {
                    console.log(response.data);
                    this.article = response.data.config
                    this.article.template_id = response.data.template_id
                })
                .catch((error)=>{
                    console.log(error)
                });
            }else{
                this.title = '新建文章';
            }

            //获取模板列表
            this.getTemplates()
            this.getCars()
            this.getCitys(0)
            this.getCountys(1)
            //关闭loading
            this.fullScreen(false)
        }
    }
</script>
