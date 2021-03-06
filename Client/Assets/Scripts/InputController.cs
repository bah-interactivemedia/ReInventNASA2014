﻿using UnityEngine;
using System.Collections;
using InControl;

public class InputController : MonoBehaviour {
	public Transform leftEye;
	public GameController controller;
	public ImageDisplay imageDisplay;

	// Use this for initialization
	void Start () {

	}
	
	// Update is called once per frame
	void Update () {
		var inputDevice = InputManager.ActiveDevice;

		if (controller.state == GameController.GameState.imageView){
			if (inputDevice.Action1.WasReleased){
				Debug.Log("Trigger Pressed!");

				Ray ray = leftEye.camera.ViewportPointToRay(new Vector3(0.5F, 0.5F, 0));
				ray.direction *= 10;

				Debug.DrawRay(ray.origin, ray.direction * 10, Color.red, 1.0f);
				RaycastHit hit;
				if (Physics.Raycast(ray, out hit) && hit.transform.tag == "Image"){
					controller.SelectImage(hit.transform);
				}
			} 

			if (inputDevice.Action4.WasReleased && !imageDisplay.isLoadingImages){
				imageDisplay.ClearDisplay();
				imageDisplay.LoadImages();
			}
		} else if (controller.state == GameController.GameState.imageInspect) {
			if (inputDevice.Action2.WasReleased){
				controller.CancelTag();
			} else if (inputDevice.Action1.WasReleased){
				controller.state = GameController.GameState.imageSelected;
			}
		} else if (controller.state == GameController.GameState.imageSelected) {
			if (inputDevice.Action2.WasReleased){
				controller.CancelTag();
			}
		}
	}
}
