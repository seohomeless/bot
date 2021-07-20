<template>
    <div class="row">
        <div class="cart">
            <strong>Корзина:</strong> <span class="count">{{carts.length}}</span>

            <div class="text-cart row" v-if="carts.length > 0" style="padding-top: 20px;">
                <div class="col-7">
                    <div class="col-12" v-for="(cart, index) in carts" style="margin-bottom: 10px; margin-top: 8px; border-bottom: 1px solid silver; height: 50px;">
                        <img :src="cart.img" style="width: 60px; float: left; padding-right: 20px;">{{cart.name}} ({{cart.price}} грн.)   <a v-on:click="deleteCart(index)" class="btn btn-sm btn-danger">Убрать</a>
                    </div>
                </div>
                <div class="col-5">
                    <div class="alert-danger form-group" v-if="validate"> * Заполните поля</div>
                    <div class="form-group">
                        <input v-model="name" class="form-control" placeholder="Ваше имя">
                    </div>
                    <div class="form-group">
                        <input v-model="phone" class="form-control"  placeholder="Телефон">
                    </div>
                    <a v-on:click="createOrder()" class="btn-cart">Заказать</a>
                </div>
            </div>

        </div>

        <div class="col-3" v-for="(product, index) in products" style="margin-bottom: 20px; margin-top: 8px;">
            <img :src="product.img" style="width: 100%; height: auto">
            <h5 style="margin-bottom: 10px; padding-top: 10px;">{{product.name}}</h5>
            <div class="text-cart row">
                <div class="col-7"><a v-on:click="addCart(index)" class="btn-cart">Купить</a></div>
                <div class="col-5"><a v-on:click="addCartClick(index)" class="btn-cart-2">В 1 клик</a></div>
            </div>
            <div class="text-center">
                <div class="price">{{product.price}} грн.</div>
                <div>Артикул: #{{product.id}}</div>
                <div>Остаток: {{product.qty}}</div>
            </div>
        </div>

    </div>
</template>

<script>
	export default {
		data(){
			return {
				products: [],
				carts: [],
				validate: false,
				name: '',
				phone: ''
			}
		},

		mounted() {
			var app = this;
			axios
				.get('https://bot.lemekha.com.ua/api/products')
				.then(response =>   {
						app.products = response.data;
					}
				);
		},

		methods: {
			addCart(index) {
				if(this.products[index].qty > 0) {
					this.products[index].qty = this.products[index].qty - 1;
					this.carts.push(this.products[index]);
				} else {
					alert('Товара нет в наличии');
				}
			},

			addCartClick(index) {
				if(this.products[index].qty > 0) {
					this.carts = [];
					this.products[index].qty = this.products[index].qty - 1;
					this.carts.push(this.products[index]);
				} else {
					alert('Товара нет в наличии');
				}
			},

			deleteCart(index) {
				if (index > -1) {
					this.carts.splice(index, 1);
				} else {
					this.carts.splice(index, 0);
				}
			},

			createOrder() {
				if(this.name.length > 0 && this.phone.length > 0) {
					this.validate = false;
					axios.get('https://bot.lemekha.com.ua/api/order/create', {
						params: {
							cart: this.carts,
							name: this.name,
							phone: this.phone,
						}
					}).then(response => {
						alert('Заказ оформлен');
						this.carts = [];
					}).catch(error => {
						console.error(error.message);
					});
				} else {
					this.validate = true;
				}
			}
		}
	}
</script>

<style>
    .btn-cart {
        width: 100%;
        height: 45px;
        line-height: 45px;
        text-align: center;
        color: black;
        background: #fff105;
        border-radius: 3px;
        display: block;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-cart-2 {
        width: 100%;
        height: 45px;
        line-height: 45px;
        text-align: center;
        color: black;
        border-radius: 3px;
        display: block;
        font-weight: bold;
        cursor: pointer;
        border: 2px solid black;
    }

    .price {
        font-size: 20px;
        padding-top: 10px;
        font-weight: bold;
    }

    .btn-cart:hover {
        text-decoration: none;
        color: black;
        background: #f8f06b;
        border-radius: 3px;
        display: block;
    }

    .btn-cart-2:hover {
        text-decoration: none;
        color: silver;
        border-radius: 3px;
        display: block;
    }

    .count {
        border-radius: 20px;
        background: #1d643b;
        color: white;
        position: relative;
        top: -10px;
        display: block;
        width: 30px;
        text-align: center;
        height: 30px;
        float: left;
        padding-top: 2px;
        left: 115px;
    }

    .cart {
        font-size: 18px;
        display: block;
        width: 100%;
        padding-bottom: 10px;
        margin-bottom: 20px;
        border-bottom: 1px solid silver;
    }

    .order {
        color: #1d68a7;
        text-decoration: underline;
        cursor: pointer;
        font-weight: bold;
    }
</style>
