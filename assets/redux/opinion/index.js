import { combineReducers } from 'redux';

import show from './show/opinionShowReducer';
import list from './list/opinionListReducer'
import update from './update/updateOpinionReducer';
import del from './delete/deleteOpinionReducer';
import create from './create/createOpinionReducer';

export default combineReducers({
    show,
    list,
    update,
    del,
    create
});
