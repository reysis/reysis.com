import React, {useEffect} from 'react';
import PropTypes from 'prop-types';
import MyDropzone from '../layouts/MyDropzone'
import {useDispatch, useSelector} from "react-redux";
import {userShow} from "../../redux/user/show/userShowActions";
import {Container, Jumbotron} from "react-bootstrap";
import TabUserProfile from "../layouts/TabUserProfile";
import ProfilePicture from "../layouts/ProfilePicture";

const UserProfile = () => {
    const user = useSelector(state => state.auth.user);

    return (
        <div>
            {
                user &&
                <div>
                    <Jumbotron fluid>
                        <div className="user-profile-header">
                            <h1 className="mt-4 mb-5">Perfil del Usuario <span>{user['persona']['nombre']}</span></h1>
                        </div>
                        <ProfilePicture img={user['profilePicture']['contentUrl']}/>
                    </Jumbotron>
                    <Container className="user-profile-tab">
                        <TabUserProfile user={user}/>
                    </Container>
                </div>
            }
            {/*<MyDropzone />*/}
        </div>
    );
}

export default UserProfile;