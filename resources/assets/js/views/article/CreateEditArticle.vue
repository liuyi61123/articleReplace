<template>
    <el-row :gutter="20">
        <el-form label-width="80px">
            <el-col :span="24">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>{{title}}</span>
                    </div>
                    <div class="text item">
                        <el-form-item label="名称">
                            <el-col :span="24">
                                <el-input v-model="article.name" placeholder="名称"></el-input>
                            </el-col>
                        </el-form-item>
                        <el-form-item label="描述">
                            <el-col :span="24">
                                <el-input v-model="article.desc" placeholder="详细描述"></el-input>
                            </el-col>
                        </el-form-item>
                        <el-form-item label="模板">
                            <el-col :span="24">
                                <el-select filterable style="width:100%" v-model="article.template_id" placeholder="请选择模版" @change="changeTemplate">
                                    <el-option  v-for="template in templates"
                                      :key="template.id"
                                      :label="template.name"
                                      :value="template.id">
                                  </el-option>
                                </el-select>
                            </el-col>
                        </el-form-item>
                        <el-form-item v-if="article.fixed_params.cityShow" label="省">
                            <el-col :span="8">
                                <el-select v-model="article.fixed_params.province.data" placeholder="请选择" @change="changeProvince">
                                    <el-option v-for="province in provinces"
                                    :key="province.id"
                                    :label="province.name"
                                    :value="province.id">
                                    </el-option>
                                </el-select>
                            </el-col>
                            <el-col :span="3">
                                <el-input-number v-model="article.fixed_params.province.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            </el-col>
                            <el-col :span="24">
                                <el-switch
                                    v-model="article.fixed_params.province.isTitle"
                                    active-text="在title中显示"
                                    inactive-text="不在title中显示">
                                </el-switch>
                            </el-col>
                        </el-form-item>
                        <el-form-item v-if="article.fixed_params.cityShow" label="市">
                            <el-col :span="8">
                                <el-select v-model="article.fixed_params.city.data" placeholder="请选择" @change="changeCity">
                                    <el-option  v-for="city in citys"
                                    :key="city.id"
                                    :label="city.name"
                                    :value="city.id">
                                    </el-option>
                                </el-select>
                            </el-col>
                            <el-col :span="3">
                                <el-input-number v-model="article.fixed_params.city.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            </el-col>
                            <el-col :span="24">
                                <el-switch
                                    v-model="article.fixed_params.city.isTitle"
                                    active-text="在title中显示"
                                    inactive-text="不在title中显示">
                                </el-switch>
                            </el-col>
                        </el-form-item>
                        <el-form-item v-if="article.fixed_params.cityShow" label="区">
                            <el-col :span="24">
                                <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                <el-checkbox-group :min="1" v-model="article.fixed_params.countys.data">
                                    <el-checkbox v-for="county of countys" :label="county.name" :key="county.id" @change="handleCheckedCitiesChange"></el-checkbox>
                                </el-checkbox-group>
                            </el-col>
                            <el-col :span="3">
                            <el-input-number v-model="article.fixed_params.countys.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            </el-col>
                            <el-col :span="24">
                            <el-switch
                                v-model="article.fixed_params.countys.isTitle"
                                active-text="在title中显示"
                                inactive-text="不在title中显示">
                            </el-switch>
                            </el-col>
                        </el-form-item>
                        <el-form-item v-if="article.fixed_params.carShow" label="汽车">
                        <div v-for="(param,index) in article.fixed_params.cars.data" :key="index"> 
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
                                    <div class="line" v-for="(model,key) in cars[param.brand].models" :key="key">
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
                                <el-input-number v-model="article.fixed_params.cars.sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            </el-col>
                            <el-col :span="24">
                                <el-switch
                                    v-model="article.fixed_params.cars.isTitle"
                                    active-text="在title中显示"
                                    inactive-text="不在title中显示">
                                </el-switch>
                            </el-col>
                            </el-form-item>
                        <el-form-item v-if="article.fixed_params.carShow" label="价格排序">
                            <el-input-number v-model="article.fixed_params.cars.price_sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            <el-col :span="24">
                            <el-switch
                                v-model="article.fixed_params.cars.priceIsTitle"
                                active-text="在title中显示"
                                inactive-text="不在title中显示">
                            </el-switch>
                            </el-col>
                        </el-form-item>

                        <el-form-item v-for="(custom_param,i) of custom_params" :key="custom_param.id" :label="custom_param.title">
                             <el-input-number v-model="article.custom_params[i].sort" controls-position="right" :min="1" :max="10"></el-input-number>
                            <el-col :span="24">
                            <el-switch
                                v-model="article.custom_params[i].isTitle"
                                active-text="在title中显示"
                                inactive-text="不在title中显示">
                            </el-switch>
                            </el-col>
                        </el-form-item>

                        <el-form-item>
                            <el-button type="primary" @click="submitFrom()">保存</el-button>
                        </el-form-item>
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
                custom_params:[],
                checkAll: false,
                isIndeterminate: true,
                templates:[
                    
                ],
                provinces:[],
                citys:[],
                countys:[],
                cars:[],
                article:{
                    name:'',
                    desc:'',
                    template_id:1,
                    custom_params:[],
                    fixed_params:
                    {
                        cityShow:false,
                        carShow:false,
                        province:{
                            sort:1,
                            data:1,
                            isTitle:true,
                        },
                        city:{
                            sort:2,
                            data:'',
                            isTitle:true,
                        },
                        countys:{
                            isTitle:true,
                            sort:3,
                            data:[]
                        },
                        cars:{
                            isTitle:true,
                            priceIsTitle:true,
                            sort:4,
                            price_sort:5,
                            data:[
                                {
                                    brand:'',
                                    models:[
                                    ]
                                }
                            ]
                        }
                    }
                },
                title:'',
                loading:''
            }
        },
        methods: {
            changeTemplate(e){
                this.fixedCustomParams(e);
            },
            fixedCustomParams(id){
                axios.get('/article/templates/'+id)
                .then((response)=> {
                    //根据模板id判断显示的参数情况
                    var fixed_params = response.data.fixed_params
                    var custom_params = response.data.custom_params

                    if(fixed_params.indexOf('city')>-1){
                        this.article.fixed_params.cityShow = true;
                    }else{
                        this.article.fixed_params.cityShow = false;
                    }
                    if(fixed_params.indexOf('car')>-1){
                        this.article.fixed_params.carShow = true;
                    }else{
                        this.article.fixed_params.carShow = false;
                    }

                    //显示自定义参数
                    if(custom_params){
                        this.getCustomParams(custom_params,true)
                    }
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            getCustomParams(custom_params,is_clear){
                axios.post('/article/api/paramids',
                {
                    ids:custom_params
                })
                .then((response)=> {
                    console.log(response)
                    this.custom_params = response.data;
                    if(is_clear){
                        this.article.custom_params = [];
                            response.data.map((item,key)=>{
                            this.article.custom_params.push({
                                id:item.id,
                                sort:6+key,
                                isTitle:true
                            })
                        })
                    }
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            changeProvince(e){
                this.countys = []
                this.article.fixed_params.countys.data = []
                this.getCitys(e)
            },
            changeCity(e){
                this.article.fixed_params.countys.data = []
                this.getCountys(e)
            },
            handleCheckAllChange(val) {
                let countys = [];
                this.countys.map((value,key)=>{
                    countys[key] = value.name
                })
                this.article.fixed_params.countys.data = val ? countys : [];
                this.isIndeterminate = false;
            },
            handleCheckedCitiesChange(value) {
                let checkedCount = value.length;
                this.checkAll = checkedCount === this.countys.length;
                this.isIndeterminate = checkedCount > 0 && checkedCount < this.countys.length;
            },
            changeCar(e,index){
                this.article.fixed_params.cars.data[index].models = []

                this.cars[e].models.map((value,key)=>{
                    let model = {
                        name:'',
                        min:1,
                        max:10
                    }
                    this.article.fixed_params.cars.data[index].models.push(model)
                })
            },
            addCar(){
                //添加汽车信息参数
                this.fixed_params.article.cars.data.push({
                    brand:'',
                    models:[]
                });
            },
            deleteCar(index){
                //删除汽车参数
                this.article.fixed_params.cars.data.splice(index, 1)
            },
            submitFrom(){
                this.fullScreen(true)
                axios({
                    method: this.id?'put':'post',
                    url: this.id?'/article/articles/'+this.id:'/article/articles',
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
                            window.location.href="/article/articles";
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
                           message = '系统错误:' + status
                       }
                       this.loading = false
                       this.$message.error(message)
                   });
            },
            //获取模板列表
            getTemplates(){
                axios.get('/article/templates?type=all')
                .then((response)=> {
                    this.templates = response.data
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取省区信息
            getProvinces(pid){
                axios.get('/article/api/citys/'+pid)
                .then((response)=> {
                    this.provinces = response.data
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取市区信息
            getCitys(pid){
                axios.get('/article/api/citys/'+pid)
                .then((response)=> {
                    this.citys = response.data
                    this.article.fixed_params.city.data = response.data[0].id
                    this.getCountys(this.article.fixed_params.city.data)
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取市区信息
            getCountys(pid){
                axios.get('/article/api/citys/'+pid)
                .then((response)=> {
                    this.countys = response.data
                    if(!this.id){
                        response.data.map((value,index)=>{
                            this.article.fixed_params.countys.data.push(value.name)
                        })
                    }
                })
                .catch((error)=>{
                    console.log(error);
                });
            },
            //获取品牌信息
            getCars(){
                axios.get('/article/api/cars')
                .then((response)=> {
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
                axios.get('/article/articles/'+this.id+'/edit')
                .then((response)=> {
                    console.log(response)
                    this.article = response.data.config
                    this.article.template_id = response.data.template_id
                    this.article.name = response.data.name
                    this.article.desc = response.data.desc
                    //获取城市
                    this.getCitys(response.data.config.fixed_params.province.data)
                    //获取区
                    this.getCountys(response.data.config.fixed_params.city.data)
                    //获取自定义参数
                    let custom_params = response.data.config.custom_params.map((item)=>{
                        return item.id;
                    })
                    this.getCustomParams(custom_params,false)

                })
                .catch((error)=>{
                    console.log(error)
                });
            }else{
                this.title = '新建文章';
                //获取城市
                this.getCitys(1)
                //获取区
                this.getCountys(2)
            }

            //获取模板列表
            this.getTemplates()
            //获取汽车信息
            this.getCars()
            //获取省
            this.getProvinces(0)
            //关闭loading
            this.fullScreen(false)
        }
    }
</script>
