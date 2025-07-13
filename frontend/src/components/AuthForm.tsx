import { useState } from 'react';
import { useMutation } from '@apollo/client';
import { LOGIN_MUTATION } from '../graphql/login';
import { REGISTER_MUTATION } from '../graphql/register';
import { useAuth } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';

export default function AuthForm() {
  const [email, setEmail] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [mode, setMode] = useState<'login' | 'register'>('login');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  
  const [login] = useMutation(LOGIN_MUTATION);
  const [register] = useMutation(REGISTER_MUTATION);
  const { refetch } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      if (mode === 'login') {
        const { data } = await login({ variables: { email, password } });
        
        if (data?.login?.success) {
          await refetch(); // Refresh user data
          navigate('/');
        } else {
          setError(data?.login?.message || 'Login failed');
        }
      } else {
        const { data } = await register({ variables: { email, username, password } });
        
        if (data?.register?.success) {
          setError('');
          setMode('login');
          setPassword('');
          alert(data.register.message || 'Registration successful! Please log in.');
        } else {
          setError(data?.register?.message || 'Registration failed');
        }
      }
    } catch (err) {
      setError(`Error: ${(err as Error).message}`);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ 
      maxWidth: 400, 
      margin: '2rem auto', 
      padding: '2rem',
      border: '1px solid #ddd',
      borderRadius: '8px',
      backgroundColor: '#f9f9f9'
    }}>
      <form onSubmit={handleSubmit}>
        <h2 style={{ textAlign: 'center', marginBottom: '1.5rem' }}>
          {mode === 'login' ? 'Login' : 'Register'}
        </h2>
        
        {error && (
          <div style={{ 
            color: 'red', 
            marginBottom: '1rem', 
            padding: '0.5rem',
            backgroundColor: '#ffebee',
            borderRadius: '4px'
          }}>
            {error}
          </div>
        )}

        <div style={{ marginBottom: '1rem' }}>
          <input 
            type="email" 
            placeholder="Email" 
            value={email} 
            onChange={e => setEmail(e.target.value)} 
            required
            style={{
              width: '100%',
              padding: '0.75rem',
              border: '1px solid #ddd',
              borderRadius: '4px',
              fontSize: '1rem'
            }}
          />
        </div>

        {mode === 'register' && (
          <div style={{ marginBottom: '1rem' }}>
            <input 
              type="text" 
              placeholder="Username" 
              value={username} 
              onChange={e => setUsername(e.target.value)} 
              required
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #ddd',
                borderRadius: '4px',
                fontSize: '1rem'
              }}
            />
          </div>
        )}

        <div style={{ marginBottom: '1.5rem' }}>
          <input 
            type="password" 
            placeholder="Password" 
            value={password} 
            onChange={e => setPassword(e.target.value)} 
            required
            style={{
              width: '100%',
              padding: '0.75rem',
              border: '1px solid #ddd',
              borderRadius: '4px',
              fontSize: '1rem'
            }}
          />
        </div>

        <button 
          type="submit" 
          disabled={loading}
          style={{
            width: '100%',
            padding: '0.75rem',
            backgroundColor: loading ? '#ccc' : '#007bff',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            fontSize: '1rem',
            cursor: loading ? 'not-allowed' : 'pointer'
          }}
        >
          {loading ? 'Loading...' : (mode === 'login' ? 'Login' : 'Register')}
        </button>
        
        <p style={{ marginTop: '1rem', textAlign: 'center' }}>
          {mode === 'login' ? (
            <>
              No account?{' '}
              <button 
                type="button" 
                onClick={() => {
                  setMode('register');
                  setError('');
                }}
                style={{
                  background: 'none',
                  border: 'none',
                  color: '#007bff',
                  cursor: 'pointer',
                  textDecoration: 'underline'
                }}
              >
                Register
              </button>
            </>
          ) : (
            <>
              Have an account?{' '}
              <button 
                type="button" 
                onClick={() => {
                  setMode('login');
                  setError('');
                }}
                style={{
                  background: 'none',
                  border: 'none',
                  color: '#007bff',
                  cursor: 'pointer',
                  textDecoration: 'underline'
                }}
              >
                Login
              </button>
            </>
          )}
        </p>
      </form>
    </div>
  );
}