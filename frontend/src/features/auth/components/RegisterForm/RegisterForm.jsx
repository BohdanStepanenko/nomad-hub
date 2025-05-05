import { useState, useContext } from 'react';
import axios from 'axios';
import {Link, useNavigate} from 'react-router-dom';
import AuthLayout from '../AuthLayout/AuthLayout';
import Button from '../../../../shared/ui/Button/Button';
import Input from '../../../../shared/ui/Input/Input';
import Spinner from "../../../../shared/ui/Spinner/Spinner";
import { UserContext } from '../../../../context/UserContext';
import styles from './RegisterForm.module.css';
import {toast} from "react-toastify";
import {FiEye, FiEyeOff} from "react-icons/fi";

const RegisterForm = () => {
    const navigate = useNavigate();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const { setAuthStatus } = useContext(UserContext);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        if (password !== confirmPassword) {
            toast.error('Passwords do not match')
            setIsLoading(false);
            return;
        }

        try {
            await axios.post(
            `${process.env.REACT_APP_API_URL}/auth/register`, {
                email,
                password,
                password_confirmation: confirmPassword
            });

            setAuthStatus(true);
            toast.success('Registration successful');
            navigate('/verify-email-prompt');
        } catch (error) {
            toast.error(error.response?.data?.message || 'Registration failed');
        } finally {
            setIsLoading(false);
        }
    };

    const handleGoogleRegister = () => {
        window.location.href = `${process.env.REACT_APP_API_URL}/auth/google/redirect`;
    };

    return (
        <AuthLayout title="Create Account">
            <form onSubmit={handleSubmit}>

                <div>
                    <Input
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        label="Email address"
                        required
                        placeholder="Enter your email"
                    />
                </div>

                <div className={styles.passwordInputContainer}>
                    <Input
                        type={showPassword ? 'text' : 'password'}
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        label="Password"
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
                        label="Confirm Password"
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
                    {isLoading ? (
                        <div className={styles.buttonContent}>
                            <Spinner size="sm" />
                            <span>Creating Account...</span>
                        </div>
                    ) : 'Register'}
                </Button>

                <div className={styles.divider}>
                    <span>or continue with</span>
                </div>

                <Button
                    type="button"
                    className={styles.googleBtn}
                    onClick={handleGoogleRegister}
                    variant="secondary"
                >
                    <img
                        src="/google.svg"
                        alt="Google"
                        className={styles.googleIcon}
                    />
                    Sign up with Google
                </Button>

                <div className={styles.links}>
                    <Link to="/login">Already have an account? Sign In</Link>
                </div>
            </form>
        </AuthLayout>
    );
};

export default RegisterForm;