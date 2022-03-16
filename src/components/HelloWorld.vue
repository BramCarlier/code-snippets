<template>
    <div id="app">
        <banner/>
        <side-navigation/>
        <div class="cardsContainer">
            <img :alt="`${card.color} ${card.number}`"
                 :src="getCardImage(card)"
                 v-for="(card, index) in cards"
                 :key="index"/>
        </div>
        <button id="next"
                @click.prevent="getNextCard()"
                :disabled="noMoreCardsAvailable || endGame"
                v-text="nextCardAvailability"></button>
        <button id="endOrReset"
                @click.prevent="endOrResetGame()"
                v-text="endOrResetText"></button>
        <p v-text="countTotal"></p>
    </div>
</template>

<script>
    import collect from 'collect.js';
    import Vue from 'vue';

    export default {
        data: () => ({
            cards: [],
            nextCardAvailability: 'Get next card',
            endGame: false,
            endOrResetText: 'No more cards needed',
        }),

        created() {
            this.getFirstTwoCards();
        },

        methods: {
            getFirstTwoCards() {
                for (let i = 0; i < 2; i++) {
                    this.getNextCard();
                }
            },
            getNextCard() {
                const newIndex = this.cards.length;

                let number = Math.floor(Math.random() * 13) + 1;
                let value = number > 10 ? 10 : number;
                let color = Math.floor(Math.random() * 2) + 1 === 1 ? 'black' : 'red';

                let alreadyInCardsArray = collect(this.cards).where('number', number).where('color', color);

                while (alreadyInCardsArray.items.length > 0) {
                    number = Math.floor(Math.random() * 13) + 1;
                    value = number > 10 ? 10 : number;
                    color = Math.floor(Math.random() * 2) + 1 === 1 ? 'black' : 'red';

                    alreadyInCardsArray = collect(this.cards).where('number', number).where('color', color);
                }

                let nextCard = {
                    number: number,
                    value: value,
                    color: color
                };

                Vue.set(this.cards, newIndex, nextCard);

                if (this.noMoreCardsAvailable) {
                    this.nextCardAvailability = 'No more cards available';
                    this.endOrResetGame();
                }
            },

            endOrResetGame() {
                this.endGame = !this.endGame;

                if (this.endGame) {
                    this.endOrResetText = 'Reset game';
                } else {
                    this.endOrResetText = 'No more cards needed';
                    this.cards = [];
                    this.nextCardAvailability = 'Get next card';
                    this.getFirstTwoCards();
                }
            },

            getCardImage(card){
                return `./img/${card.number}_${card.color}.png`;
            }
        },

        computed: {
            countTotal() {
                let total = 0;

                for (const card of this.cards) {
                    total += card.value
                }

                const aces = collect(this.cards).where('value', 1);

                return aces.items.length !== 0 && (total + 10 <= 21) ? `${total} or ${total + 10}` : total;
            },

            noMoreCardsAvailable() {
                return this.cards.length >= 7 || this.countTotal >= 21
            },
        }
    }
</script>

<style>
    #app {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    #next {
        background-color: red; /*#FCE300*/
        color: white;
        padding: 1em 1.5em;
        margin: 1em;
        text-decoration: none;
        text-transform: uppercase;
    }

    #next:hover {
        background-color: #555;
        color: white;
    }

    #next:disabled {
        background-color: lightgrey;
        color: white;
    }

    #endOrReset {
        background-color: #555; /*#FCE300*/
        color: white;
        padding: 1em 1.5em;
        margin: 1em;
        text-decoration: none;
        text-transform: uppercase;
    }

    #endOrReset:hover {
        background-color: red;
        color: white;
    }

    .cardsContainer {
        margin-bottom: 50px;
    }
</style>