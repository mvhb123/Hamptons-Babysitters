//
//  KidsProfileViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "AsyncImageView.h"
#import "RegistrationViewController.h"
#import "SMUIScrollView.h"
@interface KidsProfileViewController : AppBaseViewController<UIImagePickerControllerDelegate,UINavigationControllerDelegate,UIActionSheetDelegate,NIDropDownDelegate>
{
   
    __weak IBOutlet UILabel *lbl_hlpHintMessage;
    __weak IBOutlet UILabel *lbl_favouriteBook;
    __weak IBOutlet UILabel *lbl_favouriteCartoon;
    __weak IBOutlet UILabel *lbl_favouriteFood;
    __weak IBOutlet UILabel *lbl_helpFullHint;
    __weak IBOutlet UILabel *lbl_medication;
    __weak IBOutlet UILabel *lbl_alergies;
    __weak IBOutlet UILabel *lbl_specialNeeds;
    __weak IBOutlet UILabel *lbl_name;
    __weak IBOutlet UILabel *lbl_DOB;
    __weak IBOutlet UILabel *lbl_sex;
    __weak IBOutlet UIView *view_mainBG;
    __weak IBOutlet UITextField *txt_medication;
    __weak IBOutlet UITextView *txtView_helpFullHint;
    __weak IBOutlet AsyncImageView *view_kidsProfile;
    __weak IBOutlet UIButton *btn_addChild;
    __weak IBOutlet UIButton *btn_medicationNo;
    __weak IBOutlet UIButton *btn_medicationYes;
    __weak IBOutlet UIButton *btn_alergisNo;
    __weak IBOutlet UIButton *btn_alergisYes;
    __weak IBOutlet UIButton *btn_specialNeedsNo;
    __weak IBOutlet UIButton *btn_specialNeedsYes;
    __weak IBOutlet UITextField *txt_favouriteBook;
    __weak IBOutlet UITextField *txt_favouriteCortoon;
    __weak IBOutlet UITextField *txt_fevoriteFood;
    __weak IBOutlet UITextField *txt_alerfies;
    __weak IBOutlet UITextField *txt_specialNeeds;
    __weak IBOutlet UIButton *btn_sex;
    __weak IBOutlet UIImageView *img_kidsProfile;
    __weak IBOutlet UIButton *btn_choosePhoto;
    __weak IBOutlet UIDatePicker *datePicker;
    __weak IBOutlet UITextField *txt_name;
    __weak IBOutlet UITextField *txt_dob;
    __weak IBOutlet UIView *viewChildOtherInfo;
    __weak IBOutlet UIButton *btn_relationShip;
    __weak IBOutlet NSLayoutConstraint *consForChildInfo;
    __weak IBOutlet UILabel *lbl_RelationShip;
    __weak IBOutlet UILabel *lbl_ParentName;
    __weak IBOutlet UILabel *lbl_parentContact;
    __weak IBOutlet UITextField *txt_parentName;
    __weak IBOutlet UITextField *txt_parentContact;
    __weak IBOutlet UILabel *lbl_disclaimerText;
    __weak IBOutlet UIButton *btn_specialNeed;
    __weak IBOutlet SMUIScrollView *scrlView;
    

    
    NSString *str_alergiesStatus;
    NSString *str_medicatorStatus;
    NSString *str_specialNeedsStatus;
    NSString *str_alergies;
    NSString *str_medication;
    NSString *str_specialNeeds;
    NSData *imageData;
    RegistrationViewController *registrationView;
    NSMutableDictionary *dict_kidsProfile;
    NSMutableArray *array_ChildProfilePic;
    NSString *uniqueId;
    int cheValue;
    int j;
    IBOutlet UIView *view_dob;
    AppDelegate *AppDel;
    BOOL hasNewImageTaken;
    int childCount;
    float contentHight;
    
}
//@property(nonatomic,assign)Parent *parentInfo;
@property(nonatomic,weak)Children *childrenInfo;
@property(nonatomic)int checkValue;
@property(strong,nonatomic)NSMutableDictionary *dict_parentRecord;
@property(strong,nonatomic)NSMutableDictionary *dict_updateChildRecordData;
@property (strong,nonatomic) NIDropDown *dropDown;
@property (strong,nonatomic) NIDropDown *dropDownRelationShip;
@property (strong,nonatomic) NIDropDown *dropDownSpecialNeed;
@property (strong,nonatomic) NSArray *array_relationShip;
@property (strong,nonatomic) NSArray *array_specialNeed;
@property (nonatomic,strong)NumberFormatter *numFormatter;
@property(nonatomic,strong)NSString *jobId;


- (IBAction)onClickAddChild:(id)sender;
- (IBAction)onClickChooseDOB:(id)sender;
- (IBAction)onClickDoneDOB:(id)sender;
- (IBAction)onClickCancelDOB:(id)sender;
- (IBAction)onClickChoosePhoto:(id)sender;
- (IBAction)onClickSelectSex:(id)sender;
- (IBAction)onClickSelectYesNo:(id)sender;
- (IBAction)onClickRelationShip:(id)sender;
- (IBAction)onClickSpecialNeed:(id)sender;

@end
