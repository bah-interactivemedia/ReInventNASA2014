using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using InControl;
using UnityEngine.UI;
using UnityEngine.Events;

public class MenuInput : MonoBehaviour {
	public NASAMenuItem[] menuItems;

	private List<Button> buttons;

	private Button selectedButton;

	// Use this for initialization
	void Start () {
		buttons = new List<Button>();
		foreach (NASAMenuItem m in menuItems){
			buttons.Add(m.GetComponent<Button>());
			Debug.Log(m.direction);
		}
	}

	// This is some garbage code to make this thing work, but I'd rather do this than a radial menu during a hackathon

	// Update is called once per frame
	void Update () {
		var inputDevice = InputManager.ActiveDevice;

		var direction = NASAMenuItem.RadialDirection.none;

		if (inputDevice.LeftStickY > .5f|| inputDevice.DPadUp){
			direction = NASAMenuItem.RadialDirection.up;
		} else if (inputDevice.LeftStickX > .5f|| inputDevice.DPadRight){
			direction = NASAMenuItem.RadialDirection.right;
		} else if (inputDevice.LeftStickY < -.5f|| inputDevice.DPadDown){
			direction = NASAMenuItem.RadialDirection.down;
		} else if (inputDevice.LeftStickX < -.5f|| inputDevice.DPadLeft){
			direction = NASAMenuItem.RadialDirection.left;
		}

		//Debug.Log(inputDevice.LeftStickX + ", " + inputDevice.LeftStickY + " : " + direction);

		for (int i = 0; i < menuItems.Length; i++){
			if(menuItems[i].direction == direction){
				buttons[i].Select();
				selectedButton = buttons[i];
			}
		}

		if (inputDevice.Action1.WasReleased && selectedButton != null) {
			Debug.Log("Invoking");
			selectedButton.onClick.Invoke();
		}
	}
}
