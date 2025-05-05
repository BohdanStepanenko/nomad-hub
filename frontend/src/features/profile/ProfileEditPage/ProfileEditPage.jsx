import React, { useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { UserContext } from '../../../context/UserContext';
import Header from '../../../components/Header/Header';
import styles from './ProfileEditPage.module.css';
import Button from '../../../shared/ui/Button/Button';
import Input from '../../../shared/ui/Input/Input';
import Spinner from '../../../shared/ui/Spinner/Spinner';
import { toast } from 'react-toastify';
import { useNavigate } from 'react-router-dom';
import { FiEye, FiEyeOff, FiPlus, FiTrash2 } from 'react-icons/fi';
import Select from '../../../shared/ui/Select/Select';

const ProfileEditPage = () => {
    const navigate = useNavigate();
    const [showCurrentPassword, setShowCurrentPassword] = useState(false);
    const [showNewPassword, setShowNewPassword] = useState(false);
    const [showConfirmNewPassword, setShowConfirmNewPassword] = useState(false);
    const { user, updateUser } = useContext(UserContext);
    const [password, setPassword] = useState({
        current: '',
        new: '',
        confirm: ''
    });
    const [isLoading, setIsLoading] = useState(false);
    const [isLoadingData, setIsLoadingData] = useState(true);

    // Self Assessment
    const [assessments, setAssessments] = useState([]);
    const [skills, setSkills] = useState([]);
    const [levels, setLevels] = useState([]);
    const [userSkillsExists, setUserSkillsExists] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [skillsRes, levelsRes, assessmentsRes] = await Promise.all([
                    axios.get(`${process.env.REACT_APP_API_URL}/data/skills`, {
                        headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
                    }),
                    axios.get(`${process.env.REACT_APP_API_URL}/data/levels`, {
                        headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
                    }),
                    axios.get(`${process.env.REACT_APP_API_URL}/user-skills`, {
                        headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
                    }),
                ]);

                setSkills(skillsRes.data.data.map(skill => ({ value: skill.id, label: skill.name })));
                setLevels(levelsRes.data.data.map(level => ({ value: level.id, label: level.name })));

                if (assessmentsRes.data.data.length > 0) {
                    setAssessments(
                        assessmentsRes.data.data.map(item => ({
                            id: item.id,
                            skillId: item.skill.id,
                            levelId: item.level.id,
                            selfAssessment: item.selfAssessment,
                        }))
                    );
                    setUserSkillsExists(true);
                } else {
                    setUserSkillsExists(false);
                }
            } catch (error) {
                toast.error('Failed to load profile data');
            } finally {
                setIsLoadingData(false);
            }
        };

        fetchData();
    }, []);


    const handleAddAssessment = () => {
        setAssessments([...assessments, { id: null, skillId: '', levelId: '', selfAssessment: 0 }]);
    };

    const handleRemoveAssessment = async (index) => {
        const assessment = assessments[index];
        if (assessment.id) {
            try {
                setIsLoading(true);
                await axios.delete(`${process.env.REACT_APP_API_URL}/user-skills/${assessment.id}`, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` },
                });
                toast.success('Assessment deleted successfully');
            } catch (error) {
                toast.error('Failed to delete assessment');
                return;
            } finally {
                setIsLoading(false);
            }
        }
        setAssessments(assessments.filter((_, idx) => idx !== index));
    };

    const handleSaveAssessments = async () => {
        try {
            setIsLoading(true);

            // Разделяем оценки на новые и существующие
            const newAssessments = assessments.filter(item => !item.id);
            const existingAssessments = assessments.filter(item => item.id);

            // Создаем новые оценки
            if (newAssessments.length > 0) {
                await axios.post(
                    `${process.env.REACT_APP_API_URL}/user-skills`,
                    {
                        assessments: newAssessments.map(a => ({
                            skillId: parseInt(a.skillId),
                            levelId: parseInt(a.levelId),
                            selfAssessment: a.selfAssessment
                        }))
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('authToken')}`
                        }
                    }
                );
            }

            // Обновляем существующие оценки
            const updateRequests = existingAssessments.map(assessment =>
                axios.put(
                    `${process.env.REACT_APP_API_URL}/user-skills/${assessment.id}`,
                    {
                        skillId: parseInt(assessment.skillId),
                        levelId: parseInt(assessment.levelId),
                        selfAssessment: assessment.selfAssessment
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('authToken')}`
                        }
                    }
                )
            );

            await Promise.all(updateRequests);

            // Обновляем данные после сохранения
            const { data } = await axios.get(`${process.env.REACT_APP_API_URL}/user-skills`, {
                headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
            });

            const updatedAssessments = data.data.map(item => ({
                id: item.id,
                skillId: item.skill.id.toString(),
                levelId: item.level.id.toString(),
                selfAssessment: item.selfAssessment
            }));

            setAssessments(updatedAssessments);
            toast.success('Assessments saved successfully');

        } catch (error) {
            console.error('Save error:', error);
            toast.error('Failed to save assessments');
        } finally {
            setIsLoading(false);
        }
    };

    const renderRatingStars = (rating, index) => {
        return [1, 2, 3, 4, 5].map((star) => (
            <span
                key={star}
                className={`${styles.star} ${star <= rating ? styles.filledStar : styles.emptyStar}`}
                onClick={() =>
                    setAssessments(
                        assessments.map((a, idx) =>
                            idx === index ? { ...a, selfAssessment: star } : a
                        )
                    )
                }
            >
                ★
            </span>
        ));
    };

    const handleAvatarUpload = async (e) => {
        const file = e.target.files[0];
        const formData = new FormData();
        formData.append('avatar', file);

        setIsLoading(true);
        try {
            const response = await axios.post(
                `${process.env.REACT_APP_API_URL}/profile/avatar`,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${localStorage.getItem('authToken')}`
                    }
                }
            );

            updateUser({
                ...user,
                avatarUrl: response.data.avatarUrl
            });
            toast.success('Avatar updated successfully', {
                onClose: () => window.location.reload()
            });
        } catch (err) {
            console.error('Avatar upload error:', err);
            toast.error('Avatar upload failed');
        } finally {
            setIsLoading(false);
        }
    };

    const handleAvatarDelete = async () => {
        setIsLoading(true);
        try {
            await axios.delete(`${process.env.REACT_APP_API_URL}/profile/avatar`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('authToken')}`
                }
            });
            updateUser({ ...user, avatarUrl: null });
            toast.success('Avatar deleted successfully');
        } catch (err) {
            console.error('Avatar delete error:', err);
            toast.error('Avatar deletion failed');
        } finally {
            setIsLoading(false);
        }
    };

    const handleNameUpdate = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        try {
            await axios.put(
                `${process.env.REACT_APP_API_URL}/profile/name`,
                { name: user?.name || '' },
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('authToken')}`
                    }
                }
            );
            toast.success('Name updated successfully');
        } catch (err) {
            console.error('Name update error:', err);
            toast.error('Name update failed');
        } finally {
            setIsLoading(false);
        }
    };

    const handleEmailUpdate = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        try {
            await axios.put(
                `${process.env.REACT_APP_API_URL}/profile/email`,
                { email: user?.email },
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('authToken')}`
                    }
                }
            );
            localStorage.removeItem('authToken');
            toast.success('Email updated successfully. Check your inbox for the verification link');
            setTimeout(() => navigate('/login'), 3000);
        } catch (err) {
            console.error('Email update error:', err);
            toast.error('Email update failed');
        } finally {
            setIsLoading(false);
        }
    };

    const handlePasswordUpdate = async (e) => {
        e.preventDefault();
        if (password.new !== password.confirm) {
            toast.error('Passwords do not match');
            return;
        }

        setIsLoading(true);
        try {
            await axios.put(
                `${process.env.REACT_APP_API_URL}/profile/password`,
                {
                    currentPassword: password.current,
                    newPassword: password.new,
                    newPassword_confirmation: password.confirm
                },
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('authToken')}`
                    }
                }
            );
            localStorage.removeItem('authToken');
            toast.success('Password updated successfully');
            setPassword({ current: '', new: '', confirm: '' });
            setTimeout(() => navigate('/login'), 3000);
        } catch (err) {
            console.error('Password update error:', err);
            toast.error('Password update failed');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className={styles.container}>
            <Header />
            <main className={styles.content}>
                {isLoadingData ? (
                    <div className={styles.loadingContainer}>
                        <Spinner size="lg" />
                    </div>
                ) : (
                    <>
                        <h1 className={styles.pageTitle}>Edit Profile</h1>

                        <div className={styles.formsGrid}>
                            {/* Avatar Section */}
                            <div className={styles.formCell}>
                                <div className={styles.personalInfo}>
                                    <div className={styles.avatarSection}>
                                        <img
                                            src={user?.avatarUrl ? `${user.avatarUrl}?${Date.now()}` : '/default-avatar.png'}
                                            alt="Avatar"
                                            className={styles.avatar}
                                            onError={(e) => {
                                                e.target.src = '/default-avatar.png';
                                            }}
                                        />
                                        <input
                                            type="file"
                                            id="avatarUpload"
                                            accept="image/*"
                                            onChange={handleAvatarUpload}
                                            className={styles.avatarInput}
                                        />
                                        <label htmlFor="avatarUpload" className={styles.avatarLabel}>
                                            Change Avatar
                                        </label>
                                        {user?.avatarUrl && (
                                            <Button
                                                onClick={handleAvatarDelete}
                                                variant="secondary"
                                                className={styles.deleteAvatarBtn}
                                                disabled={isLoading}
                                            >
                                                Remove Avatar
                                            </Button>
                                        )}
                                    </div>
                                    <form onSubmit={handleNameUpdate} className={styles.form}>
                                        <Input
                                            label="Name"
                                            value={user?.name || ''}
                                            onChange={(e) => updateUser({ ...user, name: e.target.value })}
                                            placeholder="Your name"
                                        />
                                        <Button type="submit" disabled={isLoading}>
                                            {isLoading ? <Spinner size="sm" /> : 'Update Name'}
                                        </Button>
                                    </form>
                                </div>
                            </div>

                            {/* Skills Assessment Section */}
                            <div className={styles.formCell}>
                                <form className={styles.form}>
                                    <div className={styles.assessmentHeader}>
                                        <h2>Skills Assessment</h2>
                                        {!userSkillsExists && (
                                            <Button type="button" onClick={handleAddAssessment}>
                                                <FiPlus /> Add
                                            </Button>
                                        )}
                                    </div>
                                    {assessments.map((assessment, index) => (
                                        <div key={index} className={styles.assessmentItem}>
                                            <Select
                                                label="Skill"
                                                value={assessment.skillId}
                                                onChange={(e) =>
                                                    setAssessments(
                                                        assessments.map((a, idx) =>
                                                            idx === index ? { ...a, skillId: e.target.value } : a
                                                        )
                                                    )
                                                }
                                                options={skills}
                                            />
                                            <Select
                                                label="Level"
                                                value={assessment.levelId}
                                                onChange={(e) =>
                                                    setAssessments(
                                                        assessments.map((a, idx) =>
                                                            idx === index ? { ...a, levelId: e.target.value } : a
                                                        )
                                                    )
                                                }
                                                options={levels}
                                            />
                                            <div>{renderRatingStars(assessment.selfAssessment, index)}</div>
                                            <Button
                                                type="button"
                                                onClick={() => handleRemoveAssessment(index)}
                                            >
                                                <FiTrash2 /> Remove
                                            </Button>
                                        </div>
                                    ))}
                                    <Button
                                        type="button"
                                        onClick={handleSaveAssessments}
                                        disabled={isLoading}
                                    >
                                        {userSkillsExists ? 'Update All Assessments' : 'Save All Assessments'}
                                    </Button>
                                </form>
                            </div>

                            {/* Email Section */}
                            <div className={styles.formCell}>
                                <form onSubmit={handleEmailUpdate} className={styles.form}>
                                    <Input
                                        label="Email"
                                        value={user?.email}
                                        onChange={(e) => updateUser({ ...user, email: e.target.value })}
                                        placeholder="Your Email"
                                    />
                                    <p className={styles.formDescription}>
                                        You will be logged out and need to verify your new email
                                    </p>
                                    <Button type="submit" disabled={isLoading}>
                                        {isLoading ? <Spinner size="sm" /> : 'Update Email'}
                                    </Button>
                                </form>
                            </div>

                            {/* Password Section */}
                            <div className={styles.formCell}>
                                <form onSubmit={handlePasswordUpdate} className={styles.form}>
                                    <div className={styles.passwordInputContainer}>
                                        <Input
                                            type={showCurrentPassword ? 'text' : 'password'}
                                            value={password.current}
                                            onChange={(e) => setPassword({ ...password, current: e.target.value })}
                                            label="Current Password"
                                            placeholder="••••••••"
                                            required
                                        />
                                        <div
                                            onClick={() => setShowCurrentPassword(!showCurrentPassword)}
                                            className={styles.passwordToggleIcon}
                                        >
                                            {showCurrentPassword ? <FiEyeOff size={20} /> : <FiEye size={20} />}
                                        </div>
                                    </div>

                                    <div className={styles.passwordInputContainer}>
                                        <Input
                                            type={showNewPassword ? 'text' : 'password'}
                                            value={password.new}
                                            onChange={(e) => setPassword({ ...password, new: e.target.value })}
                                            label="New Password"
                                            placeholder="••••••••"
                                            required
                                        />
                                        <div
                                            onClick={() => setShowNewPassword(!showNewPassword)}
                                            className={styles.passwordToggleIcon}
                                        >
                                            {showNewPassword ? <FiEyeOff size={20} /> : <FiEye size={20} />}
                                        </div>
                                    </div>

                                    <div className={styles.passwordInputContainer}>
                                        <Input
                                            type={showConfirmNewPassword ? 'text' : 'password'}
                                            value={password.confirm}
                                            onChange={(e) => setPassword({ ...password, confirm: e.target.value })}
                                            label="Confirm New Password"
                                            placeholder="••••••••"
                                            required
                                        />
                                        <div
                                            onClick={() => setShowConfirmNewPassword(!showConfirmNewPassword)}
                                            className={styles.passwordToggleIcon}
                                        >
                                            {showConfirmNewPassword ? <FiEyeOff size={20} /> : <FiEye size={20} />}
                                        </div>
                                    </div>

                                    <p className={styles.formDescription}>
                                        You will be logged out after password change
                                    </p>
                                    <Button type="submit" disabled={isLoading}>
                                        {isLoading ? <Spinner size="sm" /> : 'Update Password'}
                                    </Button>
                                </form>
                            </div>
                        </div>
                    </>
                )}
            </main>
        </div>
    );
};

export default ProfileEditPage;