import { Component, Mixin } from 'src/core/shopware';
import template from './sw-cms-el-image.html.twig';
import './sw-cms-el-image.scss';

Component.register('sw-cms-el-image', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('image');
        }
    }
});
