//
//  SitterRequirementViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"

@interface SitterRequirementViewController : AppBaseViewController
{

    __weak IBOutlet UIButton *btn_save;
    __weak IBOutlet UILabel *lbl_bottom;
    __weak IBOutlet UITableView *tbl_groupName;
    __weak IBOutlet UILabel *lbl_langaugeType;
    __weak IBOutlet UIButton *btn_checked;
    IBOutlet UIView *view_sitterRequirement;
    NSMutableArray *array_jobPreferList;
    NSMutableArray *array_sitterRequirement;
    NSMutableArray *array_langauges;
    NSMutableArray *array_otherPreferences;
    NSMutableArray *array_selectedPrefrence ;

 
    
}
@property(nonatomic,strong)NSMutableDictionary *dict_sitterRequirement;
@property(nonatomic,strong)NSMutableArray *array_prefrences;
@property(nonatomic,strong)NSMutableArray *array_childPreference;
@property(nonatomic)int checkValue;
- (IBAction)onClickChecked:(id)sender;
- (IBAction)onClickSave:(id)sender;

@end
