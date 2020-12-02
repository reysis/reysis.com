import {
    CREATE_TURNO_REQUEST,
    CREATE_TURNO_SUCCESS,
    CREATE_TURNO_ERROR,
    CREATE_TURNO_CLEAR_ERROR
} from './createTurnoTypes'

const initialState = {
    loading: false,
    turno: null,
    error: null
}

const createTurnoReducer = (state = initialState, { type, payload }) => {
    switch (type) {
        case CREATE_TURNO_REQUEST:
            return {
                ...state,
                loading: true
            }
        case CREATE_TURNO_SUCCESS:
            return {
                loading: false,
                turno: payload,
                error: null
            }
        case CREATE_TURNO_ERROR:
            return {
                loading: false,
                turno: null,
                error: payload
            }
        case CREATE_TURNO_CLEAR_ERROR:
            return {
                ...state,
                error: null
            }
        default:
            return state
    }
}

export default createTurnoReducer