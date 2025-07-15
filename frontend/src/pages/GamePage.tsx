import { useState, useEffect } from 'react';
import { useMutation } from '@apollo/client';
import { useLocation, useNavigate } from 'react-router-dom';
import { SAVE_PERSON, END_GAME } from '../graphql/game';
import Header from '../components/Header';
import LoadingSpinner from '../components/LoadingSpinner';

interface HistoricPerson {
  id: number;
  gameStateId: number;
  name: string;
  deathDate: string;
}

interface GameState {
  id: number;
  userId: number;
  timelineAccuracy: number;
  isCompleted: boolean;
  createdAt: string;
  completedAt: string | null;
  people: HistoricPerson[];
}

interface GameResult {
  gameStateId: number;
  timelineAccuracy: number;
  eventResults: string[];
  peopleSaved: number;
  totalPeople: number;
  message: string;
}

interface LocationState {
  gameState?: GameState;
}

export default function GamePage() {
  const location = useLocation() as { state: LocationState };
  const navigate = useNavigate();
  const [gameState, setGameState] = useState<GameState | null>(null);
  const [gameResult, setGameResult] = useState<GameResult | null>(null);
  const [selectedPerson, setSelectedPerson] = useState<HistoricPerson | null>(null);

  const [savePerson, { loading: savingPerson }] = useMutation(SAVE_PERSON);
  const [endGame, { loading: endingGame }] = useMutation(END_GAME);

  // Get game state from navigation
  useEffect(() => {
    if (location.state?.gameState) {
      setGameState(location.state.gameState);
    } else {
      // If no game state provided, redirect to home
      navigate('/');
    }
  }, [location.state, navigate]);

  const handleSavePerson = async (person: HistoricPerson) => {
    if (!gameState) return;
    
    try {
      const { data } = await savePerson({
        variables: {
          gameStateId: gameState.id,
          personId: person.id
        }
      });
      
      if (data.savePerson) {
        // Update the person's death date in the local state
        setGameState(prev => prev ? {
          ...prev,
          people: prev.people.map(p => 
            p.id === person.id 
              ? { ...p, deathDate: '1965-01-24' } // Winston's actual death date
              : p
          )
        } : null);
        setSelectedPerson(null);
      }
    } catch (error) {
      console.error('Error saving person:', error);
    }
  };

  const handleEndGame = async () => {
    if (!gameState) return;
    
    try {
      const { data } = await endGame({
        variables: { gameStateId: gameState.id }
      });
      
      if (data.endGame) {
        setGameResult(data.endGame);
        setGameState(prev => prev ? { ...prev, isCompleted: true } : null);
      }
    } catch (error) {
      console.error('Error ending game:', error);
      // Even if there's an error, we should show it to the user
      alert('Error activating Paradox Key: ' + (error as Error).message);
    }
  };

  const handlePlayAgain = () => {
    navigate('/', { replace: true });
  };

  const isPersonAlive = (person: HistoricPerson, checkDate: string = '1939-09-01') => {
    return new Date(person.deathDate) > new Date(checkDate);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString();
  };

  if (!gameState) {
    return (
      <div>
        <Header />
        <div style={{ padding: '2rem' }}>
          <LoadingSpinner size="large" message="Loading game..." />
        </div>
      </div>
    );
  }

  if (gameResult) {
    return (
      <div>
        <Header />
        <div style={{ padding: '2rem' }}>
          <div style={{ textAlign: 'center', padding: '2rem' }}>
            <h1>Game Complete!</h1>
            <div style={{ 
              backgroundColor: '#f8f9fa', 
              padding: '2rem', 
              borderRadius: '12px',
              margin: '2rem 0'
            }}>
              <h2>Timeline Accuracy: {gameResult.timelineAccuracy.toFixed(1)}%</h2>
              <p><strong>People Saved:</strong> {gameResult.peopleSaved} / {gameResult.totalPeople}</p>
              <p style={{ fontSize: '1.1rem', margin: '1rem 0' }}>{gameResult.message}</p>
              
              {gameResult.eventResults.length > 0 && (
                <div style={{ marginTop: '1rem' }}>
                  <h3>Event Results:</h3>
                  <ul style={{ listStyle: 'none', padding: 0 }}>
                    {gameResult.eventResults.map((result, index) => (
                      <li key={index} style={{ margin: '0.5rem 0' }}>{result}</li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
            
            <button
              onClick={handlePlayAgain}
              style={{
                padding: '1rem 2rem',
                backgroundColor: '#3498db',
                color: 'white',
                border: 'none',
                borderRadius: '8px',
                fontSize: '1.1rem',
                fontWeight: 'bold',
                cursor: 'pointer'
              }}
            >
              Play Again
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div>
      <Header />
      <div style={{ padding: '2rem' }}>
        <div style={{ marginBottom: '2rem' }}>
          <h1>World War II Timeline</h1>
          <p style={{ fontSize: '1.1rem', color: '#666' }}>
            Current Date: <strong>September 1, 1939</strong> - World War II begins
          </p>
          <p style={{ fontSize: '0.9rem', color: '#888' }}>
            Timeline Accuracy: <strong>{gameState.timelineAccuracy.toFixed(1)}%</strong>
          </p>
        </div>

        <div style={{ 
          display: 'grid', 
          gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
          gap: '1rem',
          marginBottom: '2rem'
        }}>
          {gameState.people.map(person => (
            <div
              key={person.id}
              style={{
                backgroundColor: isPersonAlive(person) ? '#d4edda' : '#f8d7da',
                border: `2px solid ${isPersonAlive(person) ? '#c3e6cb' : '#f5c6cb'}`,
                padding: '1rem',
                borderRadius: '8px',
                cursor: 'pointer'
              }}
              onClick={() => setSelectedPerson(person)}
            >
              <h3>{person.name}</h3>
              <p><strong>Death Date:</strong> {formatDate(person.deathDate)}</p>
              <p><strong>Status:</strong> {
                isPersonAlive(person) ? 'Alive during WW2' : 'Dies before WW2'
              }</p>
            </div>
          ))}
        </div>

        {selectedPerson && (
          <div style={{ 
            backgroundColor: '#f8f9fa', 
            padding: '2rem', 
            borderRadius: '12px',
            marginBottom: '2rem'
          }}>
            <h2>Selected: {selectedPerson.name}</h2>
            <p><strong>Current Death Date:</strong> {formatDate(selectedPerson.deathDate)}</p>
            <p style={{ marginBottom: '1rem' }}>
              {isPersonAlive(selectedPerson)
                ? "This person is already alive during World War II!"
                : "This person dies before World War II begins. Save them to change history!"}
            </p>
            
            <div style={{ display: 'flex', gap: '1rem' }}>
              <button
                onClick={() => handleSavePerson(selectedPerson)}
                disabled={savingPerson || isPersonAlive(selectedPerson)}
                style={{
                  padding: '0.8rem 1.5rem',
                  backgroundColor: isPersonAlive(selectedPerson) ? '#6c757d' : '#28a745',
                  color: 'white',
                  border: 'none',
                  borderRadius: '6px',
                  cursor: isPersonAlive(selectedPerson) ? 'not-allowed' : 'pointer'
                }}
              >
                {savingPerson ? 'Saving...' : isPersonAlive(selectedPerson) ? 'Already Saved' : 'Save This Person'}
              </button>
              
              <button
                onClick={() => setSelectedPerson(null)}
                style={{
                  padding: '0.8rem 1.5rem',
                  backgroundColor: '#6c757d',
                  color: 'white',
                  border: 'none',
                  borderRadius: '6px',
                  cursor: 'pointer'
                }}
              >
                Cancel
              </button>
            </div>
          </div>
        )}

        <div style={{ 
          backgroundColor: '#e8f4f8', 
          padding: '2rem', 
          borderRadius: '12px',
          marginTop: '2rem',
          textAlign: 'center',
          border: '2px solid #3498db'
        }}>
          <h3 style={{ color: '#2c3e50', marginBottom: '1rem' }}>
            Ready to Activate the Paradox Key?
          </h3>
          <p style={{ 
            color: '#555', 
            marginBottom: '1.5rem',
            lineHeight: '1.6'
          }}>
            The Paradox Key will merge your altered timeline with the original timeline, 
            permanently changing history. Any people you've saved will survive to influence 
            World War II. This action cannot be undone.
          </p>
          <button
            onClick={handleEndGame}
            disabled={endingGame}
            style={{
              padding: '1rem 2rem',
              backgroundColor: '#3498db',
              color: 'white',
              border: 'none',
              borderRadius: '8px',
              fontSize: '1.1rem',
              fontWeight: 'bold',
              cursor: 'pointer',
              boxShadow: '0 4px 6px rgba(52, 152, 219, 0.3)'
            }}
          >
            {endingGame ? 'Activating Paradox Key...' : 'Activate Paradox Key'}
          </button>
        </div>
      </div>
    </div>
  );
}
