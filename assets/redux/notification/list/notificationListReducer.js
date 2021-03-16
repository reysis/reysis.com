import {
    NOTIFICATION_LIST_REQUEST,
    NOTIFICATION_LIST_SUCCESS,
    NOTIFICATION_LIST_ERROR,
    NOTIFICATION_ITEM_READING,
    NOTIFICATION_ITEM_READED,
    NOTIFICATION_ITEM_READERROR,
    NOTIFICATION_LIST_ASSIGN,
    NOTIFICATION_ITEM_UNREADED,
    NOTIFICATION_ITEM_UNREADERROR,
    NOTIFICATION_ITEM_UNREADING
} from './notificationListTypes'

const initialState = {
    loading: false,
    notifications: [],
    totalItems: 0,
    currentPage: 1,
    lastPage: 1,
    error: ''
}

const notificationListReducer = (state = initialState, { type, payload }) => {
    switch (type) {
        case NOTIFICATION_LIST_REQUEST:
            return {
                ...state,
                loading: true
            }
        case NOTIFICATION_LIST_SUCCESS:
            return {
                loading: false,
                notifications: [...state.notifications, ...payload.notifications],
                totalItems: payload.totalItems,
                currentPage: payload.currentPage,
                lastPage: payload.lastPage,
                error: ''
            }
        case NOTIFICATION_LIST_ERROR:
            return {
                loading: false,
                notifications: [],
                totalItems: 0,
                currentPage: 1,
                lastPage: 1,
                error: payload
            }
        case NOTIFICATION_ITEM_READED:
            return {
                ...state,
                notifications: state.notifications.map(v => (v.id == payload ? { ...v, readed: true } : v))
            }
        case NOTIFICATION_ITEM_UNREADED:
            return {
                ...state,
                notifications: state.notifications.map(v => (v.id == payload ? { ...v, readed: false } : v))
            }
        case NOTIFICATION_LIST_ASSIGN:
            return{
                ...state,
                notifications: payload
            }
        default:
            return state
    }
}

export default notificationListReducer