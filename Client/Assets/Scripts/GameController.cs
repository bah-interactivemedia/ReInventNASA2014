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
	public Transform selectedImage;
	private Vector3 selectedImageOrig;
	private Vector3 selectedImageRot;

	[HideInInspector]
	public GameState state { 
		get {
			return _state;
		}set{
			_state = value;

			if (_state == GameState.imageView){
				radialMenu.SetActive(false);
			}
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

	public void ImageTagged(int imgTag = 0){
		Debug.Log("Tagged!");
		state = GameState.imageView;
		var mark = selectedImage.FindChild("Mark");
		mark.gameObject.SetActive(true);
		DeselectImage();
	}

	public void CancelTag(){
		Debug.Log("Cancel!");
		state = GameState.imageView;
		DeselectImage();
	}

	public void SelectImage(Transform image){
		state = GameState.imageSelected;

		selectedImage = image;
		selectedImageOrig = image.position;
		selectedImageRot = image.rotation.eulerAngles;

		Go.to(image, .66f, new GoTweenConfig()
		      .position(targetImagePosition.position)
		      .rotation(-Vector3.forward)
		      .onComplete(thisTransform => {
			radialMenu.SetActive(true);
		}));
	}

	public void DeselectImage(){
		radialMenu.SetActive(false);
		if (selectedImage != null){
			Go.to(selectedImage, .66f, new GoTweenConfig()
			      .position(selectedImageOrig)
			      .rotation(selectedImageRot));
		}
		selectedImage = null;
	}
}
