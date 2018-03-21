<template>
    <div class="card">
        <div class="card-header clearfix">
            <span class="float-left">参数设置</span>
            <button type="button" class="btn btn-success btn-sm float-right" @click="addParam()">添加参数</button>
        </div>
        <div class="card-body">
            <div class="" v-for="number in numbers">
                <p>{{number.id}}</p>
                <div class="form-group">
                    <input type="text" class="form-control" name="param_names[]" placeholder="参数名称{$param}">
                </div>
                <div class="form-group">
                    <textarea class="form-control" name="param_contents[]" rows="5" cols="30"  placeholder="需要替换的内容,一行一个"></textarea>
                </div>
                <button type="button" class="btn btn-danger btn-sm float-right" @click="deleteParam(number.id)">删除</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                numbers:
                [
                    {id:0}
                ],
                index:0
            }
        },
        methods: {
            addParam(){
                //添加参数
                this.index++;
                Vue.set(this.numbers, this.numbers.length, {id:this.index});
                console.log(this.index);
                console.log(this.numbers);
            },
            deleteParam(id){
                //删除参数
                this.numbers.splice(this.numbers.find( number => {
                    return number.id === id;
                }), 1);
            }
        }
    }
</script>
