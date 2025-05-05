import { useState } from 'react';
import { useLocation } from 'react-router-dom';
import axios from 'axios';
import { toast } from 'react-toastify';
import Button from "../../../../shared/ui/Button/Button";

const VerifyEmailPrompt = () => {
    const location = useLocation();
    const { email } = location.state || {};
    const [isLoading, setIsLoading] = useState(false);

    const handleResendVerification = async () => {
        setIsLoading(true);
        try {
            await axios.post(`${process.env.REACT_APP_API_URL}/auth/resend-verification`, { email });
            toast.success('Verification email resent successfully');
        } catch (error) {
            toast.error(error.response?.data?.message || 'Failed to resend verification email');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div style={{ padding: '20px', textAlign: 'center' }}>
            <h2>Email Verification Required</h2>
            <p>
                Your email is not verified. Please check your inbox for the verification link.
            </p>
            {email && (
                <p>
                    Not receiving the email?{' '}
                    <span style={{ display: 'block', marginTop: '30px' }}>
                    <Button onClick={handleResendVerification} disabled={isLoading}>
                        {isLoading ? 'Resending...' : 'Resend Verification Email'}
                    </Button>
                </span>
                </p>
            )}
        </div>
    );
};

export default VerifyEmailPrompt;
