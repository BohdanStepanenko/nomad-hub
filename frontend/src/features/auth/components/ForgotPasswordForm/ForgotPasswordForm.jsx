import { useState } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';
import AuthLayout from '../AuthLayout/AuthLayout';
import Button from '../../../../shared/ui/Button/Button';
import Input from '../../../../shared/ui/Input/Input';
import Spinner from '../../../../shared/ui/Spinner/Spinner';
import styles from './ForgotPasswordForm.module.css';
import {toast} from "react-toastify";

const ForgotPasswordForm = () => {
    const [email, setEmail] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            await axios.post(`${process.env.REACT_APP_API_URL}/auth/password/forgot`, { email });
            toast.success('Password reset link has been sent to your email');
        } catch (error) {
            toast.error(error.response?.data?.message || 'Failed to send reset link');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AuthLayout title="Reset Password">
            <form onSubmit={handleSubmit}>
                <p className={styles.description}>
                    Enter the email to which your account is linked, we will send a link there to reset your password
                </p>

                <div className={styles.formGroup}>
                    <Input
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        label="Email address"
                        required
                        placeholder="Enter your Email"
                    />
                </div>

                <Button
                    type="submit"
                    className={styles.submitBtn}
                    disabled={isLoading}
                >
                    {isLoading ? (
                        <div className={styles.buttonContent}>
                            <Spinner size="sm" />
                            <span>Sending...</span>
                        </div>
                    ) : 'Send Reset Link'}
                </Button>

                <div className={styles.links}>
                    <Link to="/login">Remember your password? Sign In</Link>
                </div>
            </form>
        </AuthLayout>
    );
};

export default ForgotPasswordForm;