require('./bootstrap');
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';

new TomSelect("#tags", {
    plugins: ['no_backspace_delete','remove_button'],
    createOnBlur: true,
    create: true
});
new TomSelect("#authors_ids", {
    plugins: ['no_backspace_delete','remove_button']
});
