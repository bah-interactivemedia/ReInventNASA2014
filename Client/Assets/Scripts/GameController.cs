using UnityEngine;
using System.Collections;
using UnityEngine.UI;

public class GameController : MonoBehaviour {
	public enum GameState{
		imageView,
		imageSelected
	}

	public Transform targetImagePosition;
	public GameObject radialMenu;

	[HideInInspector]
	public GameState state { 
		get {
			return _state;
		}set{
			_state = value;
			// Do any state specific stuff here
		}
	}
	private GameState _state;

	// Use this for initialization
	void Start () {
		state = GameState.imageView;
	}
	
	// Update is called once per frame
	void Update () {

	}

	public void SelectImage(Transform image){
		state = GameState.imageSelected;

		Go.to(image, .66f, new GoTweenConfig()
		      .position(targetImagePosition.position)
		      .rotation(-Vector3.forward)
		      .onComplete(thisTransform => {
			radialMenu.SetActive(true);
		}));
	}
}
