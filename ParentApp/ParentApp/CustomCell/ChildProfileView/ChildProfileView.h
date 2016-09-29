//
//  ChildProfileView.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 05/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AsyncImageView.h"

@interface ChildProfileView : UIView

@property (weak, nonatomic) IBOutlet UIButton *btn_previousPage;
@property (weak, nonatomic) IBOutlet UIScrollView *scrollBGForChildDetail;
@property (weak, nonatomic) IBOutlet UILabel *lbl_sexValue;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ageValue;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewSpecialNeeds;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewAlergies;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewMedications;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewHelpFullHint;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ViewFavFood;

@property (weak, nonatomic) IBOutlet UILabel *lbl_viewFavCartoon;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewFavBook;

@property (weak, nonatomic) IBOutlet UILabel *lbl_childName;
@property (weak, nonatomic) IBOutlet UILabel *lbl_age;
@property (weak, nonatomic) IBOutlet UILabel *lbl_Sex;
@property (weak, nonatomic) IBOutlet UILabel *lbl_specialNeeds;
@property (weak, nonatomic) IBOutlet UILabel *lbl_alergies;
@property (weak, nonatomic) IBOutlet UILabel *lbl_medications;
@property (weak, nonatomic) IBOutlet UITextView *txtView_specialHints;
@property (weak, nonatomic) IBOutlet UILabel *lbl_favouriteFood;
@property (weak, nonatomic) IBOutlet UILabel *lbl_favouriteCartoon;
@property (weak, nonatomic) IBOutlet UILabel *lbl_favouriteBook;
@property (weak, nonatomic) IBOutlet UIButton *btn_addChild;
@property (weak, nonatomic) IBOutlet AsyncImageView *view_childImage;
@property (weak, nonatomic) IBOutlet UIButton *btn_editProfile;
@property (weak, nonatomic) IBOutlet UIButton *btn_nextPage;
@property (weak, nonatomic) IBOutlet UILabel *lbl_relationShipHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_parentNameHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_parentContactHeading;
@property (weak, nonatomic) IBOutlet UILabel *lbl_relationShip;
@property (weak, nonatomic) IBOutlet UILabel *lbl_parentName;
@property (weak, nonatomic) IBOutlet UILabel *lbl_parentContact;
@property (weak, nonatomic) IBOutlet NSLayoutConstraint *consForFavFood;
@property (weak, nonatomic) IBOutlet NSLayoutConstraint *consForAllergies;







@end
