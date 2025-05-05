import { useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { toast } from 'react-toastify';

const VerifyEmail = () => {
    const { token } = useParams();
    const navigate = useNavigate();

    useEffect(() => {
        let isMounted = true;
        const verify = async () => {
            try {
                const response = await axios.get(`${process.env.REACT_APP_API_URL}/auth/verify-email/${token}`);
                if (isMounted) {
                    toast.success(response.data.message || 'Email verified successfully');
                    navigate('/login');
                }
            } catch (error) {
                if (isMounted) {
                    toast.error(error.response?.data?.message || 'Email verification failed');
                }
            }
        };

        if (token) {
            verify();
        } else {
            toast.error('Invalid verification link');
            navigate('/login');
        }

        return () => {
            isMounted = false;
        };
    }, [token, navigate]);

    return (
        <div>
            <p>Verifying email, please wait...</p>
        </div>
    );
};

export default VerifyEmail;
