import { useState, useEffect } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import axios from 'axios';
import AuthLayout from '../AuthLayout/AuthLayout';
import Button from '../../../../shared/ui/Button/Button';
import Input from '../../../../shared/ui/Input/Input';
import styles from './ResetPasswordForm.module.css';
import { toast } from "react-toastify";
import {FiEye, FiEyeOff} from "react-icons/fi";

const ResetPasswordForm = () => {
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();

    const token = searchParams.get('token');
    const email = searchParams.get('email');

    useEffect(() => {
        if (!token || !email) {
            navigate('/login');
        }
    }, [token, email, navigate]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        if (password !== confirmPassword) {
            toast.error('Passwords do not match');
            setIsLoading(false);
            return;
        }

        try {
            await axios.post(`${process.env.REACT_APP_API_URL}/auth/password/reset`, {
                token,
                email,
                password,
                password_confirmation: confirmPassword
            });

            localStorage.removeItem('authToken');
            toast.success('Password has been reset successfully! Redirecting to login...');
            setTimeout(() => navigate('/login'), 3000);
        } catch (error) {
            toast.error(error.response?.data?.message || 'Password reset failed');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AuthLayout title="Set New Password">
            <form onSubmit={handleSubmit}>
                <div className={styles.passwordInputContainer}>
                    <Input
                        type={showPassword ? 'text' : 'password'}
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        label="New Password"
                        placeholder="••••••••"
                        required
                    />
                    <div
                        onClick={() => setShowPassword(!showPassword)}
                        className={styles.passwordToggleIcon}
                    >
                        {showPassword ? <FiEyeOff size={20} /> : <FiEye size={20} />}
                    </div>
                </div>

                <div className={styles.passwordInputContainer}>
                    <Input
                        type={showConfirmPassword ? 'text' : 'password'}
                        value={confirmPassword}
                        onChange={(e) => setConfirmPassword(e.target.value)}
                        label="Confirm New Password"
                        placeholder="••••••••"
                        required
                    />
                    <div
                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                        className={styles.passwordToggleIcon}
                    >
                        {showConfirmPassword ? <FiEyeOff size={20} /> : <FiEye size={20} />}
                    </div>
                </div>

                <Button
                    type="submit"
                    className={styles.submitBtn}
                    disabled={isLoading}
                >
                    {isLoading ? 'Resetting...' : 'Reset Password'}
                </Button>
            </form>
        </AuthLayout>
    );
};

export default ResetPasswordForm;
