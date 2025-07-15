import { useAuth } from '../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import { useMutation, useQuery } from '@apollo/client';
import { START_NEW_GAME, CURRENT_GAME } from '../graphql/game';
import Header from '../components/Header';
import LoadingSpinner from '../components/LoadingSpinner';

export default function HomePage() {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [startNewGame, { loading: startingGame }] = useMutation(START_NEW_GAME);
  const { data: currentGameData, loading: loadingCurrentGame } = useQuery(CURRENT_GAME);

  const handleStartNewGame = async () => {
    if (!user) return;
    
    try {
      const { data } = await startNewGame({
        variables: { userId: parseInt(user.id) }
      });
      
      if (data.startNewGame) {
        // Navigate to game page with the game state
        navigate('/game', { state: { gameState: data.startNewGame } });
      }
    } catch (error) {
      console.error('Error starting new game:', error);
    }
  };

  const handleContinueGame = () => {
    if (currentGameData?.currentGame) {
      navigate('/game', { state: { gameState: currentGameData.currentGame } });
    }
  };
  /*
  const handleRefreshGames = () => {
    // Refetch the current game query
    window.location.reload();
  };
  */
  if (loadingCurrentGame) {
    return (
      <div>
        <Header />
        <LoadingSpinner size="large" message="Checking for ongoing games..." />
      </div>
    );
  }

  return (
    <div>
      <Header />
      <div style={{ padding: '2rem' }}>
        <h1>Welcome, {user?.username || 'friend'}!</h1>
        
        <div style={{ 
          marginTop: '2rem', 
          padding: '2rem', 
          backgroundColor: '#f8f9fa', 
          borderRadius: '12px',
          textAlign: 'center'
        }}>
          <h2>Paradox Key</h2>
          <p style={{ fontSize: '1.2rem', color: '#666', marginBottom: '2rem' }}>
            Travel through time and save historical figures to change the course of history!
          </p>
          
          {currentGameData?.currentGame ? (
            <div>
              <div style={{ 
                backgroundColor: '#e8f5e8', 
                padding: '1rem', 
                borderRadius: '8px', 
                marginBottom: '1rem',
                border: '1px solid #4caf50'
              }}>
                <h3 style={{ margin: '0 0 0.5rem 0', color: '#2e7d32' }}>Continue Game</h3>
                <p style={{ margin: '0', color: '#555' }}>
                  Started: {new Date(currentGameData.currentGame.createdAt).toLocaleDateString()}
                  <br />
                  Timeline Accuracy: {currentGameData.currentGame.timelineAccuracy.toFixed(1)}%
                </p>
              </div>
              <button
                onClick={handleContinueGame}
                style={{
                  padding: '1rem 2rem',
                  backgroundColor: '#4caf50',
                  color: 'white',
                  border: 'none',
                  borderRadius: '8px',
                  fontSize: '1.1rem',
                  fontWeight: 'bold',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease'
                }}
              >
                Continue Game
              </button>
            </div>
          ) : (
            startingGame ? (
              <LoadingSpinner size="large" message="Creating new game..." />
            ) : (
              <button
                onClick={handleStartNewGame}
                style={{
                  display: 'inline-block',
                  padding: '1rem 2rem',
                  backgroundColor: '#3498db',
                  color: 'white',
                  border: 'none',
                  borderRadius: '8px',
                  fontSize: '1.1rem',
                  fontWeight: 'bold',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease'
                }}
              >
                Start New Game
              </button>
            )
          )}
        </div>
        
        <div style={{ 
          marginTop: '2rem', 
          padding: '1rem', 
          backgroundColor: '#f5f5f5', 
          borderRadius: '8px' 
        }}>
          <h3>Your Profile:</h3>
          <p><strong>Email:</strong> {user?.email}</p>
          <p><strong>Username:</strong> {user?.username}</p>
          <p><strong>ID:</strong> {user?.id}</p>
        </div>
      </div>
    </div>
  );
}