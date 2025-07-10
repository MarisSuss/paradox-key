import { useState } from 'react';
import { useMutation } from '@apollo/client';
import { LOGIN_MUTATION } from './graphql/login';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [login, { data, loading, error }] = useMutation(LOGIN_MUTATION);

  const handleLogin = async () => {
    try {
      await login({ variables: { email, password } });
    } catch (e) {
      console.error('Login error:', e);
    }
  };

  return (
    <div style={{ maxWidth: '400px', margin: '2rem auto', padding: '1rem', border: '1px solid #ccc', borderRadius: '8px' }}>
      <h2>Login</h2>
      <div style={{ marginBottom: '1rem' }}>
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={e => setEmail(e.target.value)}
          style={{ width: '100%', padding: '0.5rem', marginBottom: '0.5rem' }}
        />
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={e => setPassword(e.target.value)}
          style={{ width: '100%', padding: '0.5rem' }}
        />
      </div>
      <button onClick={handleLogin} disabled={loading} style={{ width: '100%', padding: '0.5rem' }}>
        {loading ? 'Logging in...' : 'Login'}
      </button>

      {error && <p style={{ color: 'red', marginTop: '1rem' }}>Error: {error.message}</p>}
      {data && (
        <p style={{ color: data.login.success ? 'green' : 'red', marginTop: '1rem' }}>
          {data.login.message}
        </p>
      )}
    </div>
  );
}