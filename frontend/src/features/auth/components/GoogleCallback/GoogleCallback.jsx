import { useEffect, useContext } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { toast } from 'react-toastify';
import { UserContext } from '../../../../context/UserContext';

const GoogleCallback = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const { loadUserProfile } = useContext(UserContext);

    useEffect(() => {
        const queryParams = new URLSearchParams(location.search);
        const token = queryParams.get('token');
        if (token) {
            localStorage.setItem('authToken', token);
            loadUserProfile();
            navigate('/profile');
        } else {
            toast.error('Google authentication failed. Please try again.');
            navigate('/login');
        }
    }, [location, navigate, loadUserProfile]);

    return (
        <div>
            <p>Processing Google authentication...</p>
        </div>
    );
};

export default GoogleCallback;
